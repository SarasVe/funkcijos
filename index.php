<?php

require_once __DIR__ . '/vendor/autoload.php';

use OhMyBrew\BasicShopifyAPI;
use Arubacao\TldChecker\Validator;

//ini_set('display_errors', 'On');
//error_reporting(-1);

class Order
{

	/*
	 * Settings
	 */
	private $domain = 'sarotestas.myshopify.com';
	private $apiKey = '66037abc84c74449d27728102f16be37';
	private $secret = '29d74413a5e4dbffd586e63a7e97f850';

	protected $api = new BasicShopifyAPI(true); //  true sets it to private

	public function __construct(){}

	$api->setShop($domain);
	$api->setApiKey($apiKey);
	$api->setApiPassword($secret);

	// existing order id's: 947864436821, 949967028309, 950614589525, 950633332821, 950708600917, 950709715029, 950714499157
	protected $order_ID = 950614753365;
	protected $new_order_id = 949725298771;
	protected $itemId = 2131838795861;
	protected $lineItem_ID = 321312;
	protected $variant_ID = 238974;
	protected $note = 'Gateway: PayPal';

	// new order array
	$newOrder = array(
		'order' => array(
			'id' => 947864436667,
			'name' => 'vardenis',
			'email' => 'vards@mail.com',
			'note' => $note,
			'line_items' => array([
				'line_item_id' => $lineItem_ID,
				'variant_id' => $variant_ID,
				'title' => 't-shirt crazy',
				'quantity' => 1,
				'price' => 3.00
			]),
			'shipping_address' => array(
				'last_name' => 'BubLastName', //required
				'address1' => '123 Ship Street',  //required
				'phone' => '+37066655544',
	      		'city' => 'Shipsville',  //required
				'zip' => '41000',  //required
				'country' => 'France'
			)
		)
	);

	//importOrder($newOrder, $new_order_id, $api);
	/*
	 * import order, kuri pagal duota array sukuria nauja orderi
	 * su basic informacija - vardas, adresas, email, ka pirko (line_items)
	 * ir koki nors note pvz. Gateway: PayPal
	 */
	function importOrder($arr, $newID, $api)
	{
		$arr['order']['id'] = $newID;

		$put = $api->rest( 'POST', '/admin/orders.json', $arr );

		if($put->errors){

			echo "<br>[IMPORT ORDER] Got {$put->errors->status} error.<br>";
			print_r($put->errors->body);

		} else {

			echo '<br>[IMPORT ORDER] $newID was successfull<br>';
		}

	}

	// upsell items array
	$newItems = array([
			'id' => 2131838795861,
			'variant_id' => 1111,
			'title'	=> 't-shirt fun',
			'quantity'  => 1,
			'price' => 4.00
		],[
			'id' => 2131838795861,
			'variant_id' => 2222,
			'title'	=> 't-shirt fun',
			'quantity'  => 1,
			'price' => 2.00
		]
	);

	//importUpsell($order_ID, $newItems, $new_order_id, $api);
	/*
	 * import upsell, argumentai jos order_id ir upsell items.
	 * Ji turetu pasiimti originalaus orderio info pagal order_id,
	 * visa ja issisaugot i kintamaji, istrinti originalu orderi,
	 * ir sukurti nauja su ta pacia info, tik plius papildomai nauji upsell items.
	 */
	function importUpsell($orderID, $upsellItems, $newID, $api)
	{
		$orderInfo = getOrderInfo($orderID, $api);

		$order = checkString($orderInfo);

		$allItems = addItems($order, $upsellItems);

		deleteOrder($orderID, $api);

		importOrder($allItems, $newID, $api);
	}

	//addItems(getOrderInfo($order_ID, $api), $newItems);
	/*
	 * add old and new list_items
	 */
	function addItems($obj, $newData) // $list, $a)
	{
		// ***** get old items and convert to array
		$getOldItems = $obj->body->order->line_items;
		$getOldItems = json_decode(json_encode($getOldItems), true);
		//echo '<pre>'; var_dump($oldItems); echo '</pre>';

		// ***** get required data about items
		for ($i=0; $i < count($getOldItems); $i++) {

			$items[$i]['id'] = $getOldItems[$i]['id'];
			$items[$i]['variant_id'] = $getOldItems[$i]['variant_id'];
			$items[$i]['title'] = $getOldItems[$i]['title'];
			$items[$i]['quantity'] = $getOldItems[$i]['quantity'];
			$items[$i]['price'] = $getOldItems[$i]['price'];
		};

		// ***** add new items to the end of old items
		array_splice($items, count($getOldItems), 0, $newData);
		//echo '<br>List of items:<br><pre>'; var_dump($line_items); echo '</pre>';

		$getOldOrder = $obj->body->order;
		$oldOrder = json_decode(json_encode($getOldOrder), true);
		//echo '<pre>'; var_dump($oldOrder); echo '</pre>';

		$newOrder['order'] = array(
			'id' 	=> $oldOrder['id'],
			'name' => $oldOrder['name'],
			'email' => $oldOrder['email'],
			'shipping_address' => array(
				'last_name' => $oldOrder['shipping_address']['last_name'],
				'address1' => $oldOrder['shipping_address']['address1'],
				'city' 	=> $oldOrder['shipping_address']['city'],
				'zip' 	=> $oldOrder['shipping_address']['zip'],
				'country' => $oldOrder['shipping_address']['country']
			),
			'line_items' => $items
		);
		//echo '<pre>'; var_dump($newOrder); echo '</pre>';
		return $newOrder;
	}

	/*
	 * add old and new list_items
	 */
	function checkString($info)
	{
		$info = $info->order;
		$ship = $info->order->shipping_address;

		if( isset($info->id) &&
			isset($info->name) &&
			isset($info->email) &&
			isset($ship->last_name) &&
			isset($ship->address1) &&
			isset($ship->city) &&
			isset($ship->zip) &&
			isset($ship->country) )
			{
				if(Validator::endsWithTld($info->email)) return true;

			} else {
				$infoArr = json_decode(json_encode($info), true);
				$empty = array();
				$count = 0;
				foreach ($infoArr as $k => $v) {
					if( is_null($infoArr[$v]) ) {
						$empty[$count] = [$k];
						$count++;
					}
				}
				if($count === 0)
					echo "<br>something is wrong {$count}<br>";
				else {
					return $empty;
				}
			}
	}
	//getOrderInfo($order_ID, $api);//$url);
	/*
	 * get order by ID function
	 */
	function getOrderInfo($orderId, $api)
	{
		$order = $api->rest('GET', '/admin/orders/'. $orderId .'.json');

		if($order->errors){

			echo "<br>[GET ORDER INFO] Got {$order->errors->status} error.<br>";
			print_r($order->errors->body);

		} else {

			echo "<br>[GET ORDER INFO] $orderId was successfull.<br>";

			$check = checkString($order);

			if($check == true) {

				return $order;

			} else {

				echo "<br> Please fill all required info!<br>";
			}
		}
	}

	/*
	 * get all $orders
	 */
	//getAllOrders($api);

	function getAllOrders($api)
	{
		$orders = $api->rest('GET', '/admin/orders.json');

		if(isset($orders)){

			echo "<br> All orders:<br>";
			//var_dump($orders);
			return $orders;
		}
	}

	/*
	 * delete order by ID function
	 */
	//deleteOrderById($order_ID, $api);

	function deleteOrder($orderId, $api)
	{
		$del = $api->rest('DELETE', '/admin/orders/'.$orderId.'.json');

		if($del->errors){

			echo "<br>[DELETE ORDER] Got {$del->errors->status} error.<br>";
			print_r($del->errors->body);

		} else {
			echo "<br>[DELETE ORDER] $orderId was successfull.<br>";
		}
	}
}
