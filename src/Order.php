<?php
namespace src;

require_once __DIR__ . '/../vendor/autoload.php';

use OhMyBrew\BasicShopifyAPI;
use Arubacao\TldChecker\Validator;

//ini_set('display_errors', 'On');
//error_reporting(-1);

class Order
{
	// existing order id's: 947864436821, 949967028309, 950614589525, 950633332821, 950708600917, 950709715029, 950714499157
	 var $order_ID = 950614753365;
	 var $new_order_id = 949725298771;

	public function __construct(){
		//checkMembership( 'vards@mail.com' );
	}

	function getData() {

		$itemId = 2131838795861;
		$lineItem_ID = 321312;
		$variant_ID = 238974;
		$note = 'Gateway: PayPal';

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
		return $newOrder;
	}

	public function getApi()
	{
		// Settings
		$domain = 'sarotestas.myshopify.com';
		$apiKey = '66037abc84c74449d27728102f16be37';
		$secret = '29d74413a5e4dbffd586e63a7e97f850';

		$api = new BasicShopifyAPI( true ); //  true sets it to private
		$api->setShop( $domain );
		$api->setApiKey( $apiKey );
		$api->setApiPassword( $secret );

		return $api;
	}

	/*
	 * import order, kuri pagal duota array sukuria nauja orderi
	 * su basic informacija - vardas, adresas, email, ka pirko (line_items)
	 * ir koki nors note pvz. Gateway: PayPal
	 */
	 //importOrder(getData(), $new_order_id);
 	function importOrder( $arr, $newID ) // $arr pakeist getData()
	{
		$arr['order']['id'] = $newID;
		$api = getApi();

		$put = $api->rest( 'POST', '/admin/orders.json', $arr );

		if( $put->errors ){

			echo "<br>[IMPORT ORDER] Got {$put->errors->status} error.<br>";
			print_r( $put->errors->body );

		} else {

			echo '<br>[IMPORT ORDER] $newID was successfull<br>';
		}

	}

	// upsell items array
	var $newItems = array([
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

	/*
	 * import upsell, argumentai jos order_id ir upsell items.
	 * Ji turetu pasiimti originalaus orderio info pagal order_id,
	 * visa ja issisaugot i kintamaji, istrinti originalu orderi,
	 * ir sukurti nauja su ta pacia info, tik plius papildomai nauji upsell items.
	 */
	 //importUpsell(); //$order_ID, $newItems, $new_order_id);
 	function importUpsell()
	{
		$orderInfo = getOrderInfo( $order_ID );

		$order = checkString( $orderInfo );

		$allItems = addItems( $order, $newItems );

		deleteOrder( $order_ID );

		importOrder( $allItems, $new_order_id );
	}

	/*
	 * add old and new list_items
	 */
	 //addItems(getOrderInfo($order_ID), $newItems);
 	function addItems( $obj, $newData )
	{
		// ***** get old items and convert to array
		$getOldItems = $obj->body->order->line_items;
		$getOldItems = json_decode( json_encode( $getOldItems ), true);
		//echo '<pre>'; var_dump($oldItems); echo '</pre>';

		// ***** get required data about items
		for ( $i=0; $i < count( $getOldItems ); $i++ ) {

			$items[$i]['id'] = $getOldItems[$i]['id'];
			$items[$i]['variant_id'] = $getOldItems[$i]['variant_id'];
			$items[$i]['title'] = $getOldItems[$i]['title'];
			$items[$i]['quantity'] = $getOldItems[$i]['quantity'];
			$items[$i]['price'] = $getOldItems[$i]['price'];
		};
		// ***** add new items to the end of old items
		array_splice( $items, count( $getOldItems ), 0, $newData );
		//echo '<br>List of items:<br><pre>'; var_dump($line_items); echo '</pre>';
		$getOldOrder = $obj->body->order;
		$oldOrder = json_decode( json_encode( $getOldOrder ), true );
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
	function checkString( $info )
	{
		//error_log( print_r( $info, true ) );
		$info = json_decode( json_encode( $info ) );
		//$info = (object) $info;
		//$info = $info->order;
		//$ship = $info->order->shipping_address;

		if( ( isset( $info->name ) && !empty( $info->name ) ) &&
			( isset( $info->email ) && !empty( $info->email ) ) &&
			( isset( $ship->last_name ) && !empty( $info->last_name ) ) &&
			( isset( $ship->address1 ) && !empty( $info->address1 ) ) &&
			( isset( $ship->city ) && !empty( $info->city ) ) &&
			( isset( $ship->zip ) && !empty( $info->zip ) ) &&
			( isset( $ship->country ) && !empty( $info->country ) ) )
			{
				if( Validator::endsWithTld( $info->email ) ) {
					echo '\r\n Email is Valid\r\n';
					$e = $info->email;
					return $e;
				} else {
					//error_log( print_r( $info, true ) );
					$e = $this->emptyFields( $info );
					return $e; //true;
				}
			}
	}
	/*
	 * collect all empty keys and put to array
	 */
	function emptyFields( $d )
	{
		//$count = 0;
		$empty = array();

			foreach( $d as $k => $v ) {

				if( is_object( $v ) ) $this->emptyFields( $v );

				elseif( empty( $v ) ) {
						//print_r($k);
						$empty[$count] = $k;
						$count++;
						return $k;
        }
    	}
			return $empty;
	}

	/*
	 * get order by ID function
	 */
	 //getOrderInfo($order_ID);
 	function getOrderInfo( $orderId )
	{
		$api = getApi();
		$order = $api->rest( 'GET', '/admin/orders/'. $orderId .'.json' );
		//echo '<pre>'; var_dump($order); echo '</pre>';
		if( $order->errors ) {

			echo "<br>[GET ORDER INFO] Got {$order->errors->status} error.<br>";
			print_r( $order->errors->body );

		} else {

			echo "<br>[GET ORDER INFO] $orderId was successfull.<br>";

			$check = checkString( $order );

			if( $check == true ) {

				return $order;

			} else {

				echo "<br> Please fill all required info!<br>";
			}
		}
	}

	/*
	 * get all $orders
	 */
	//getAllOrders();
	function getAllOrders()
	{
		$api = getApi();
		$orders = $api->rest( 'GET', '/admin/orders.json' );

		if( isset( $orders ) ) {

			echo "<br> All orders:<br>";
			//var_dump($orders);
			return $orders;
		}
	}

	/*
	 * delete order by ID function
	 */
	//deleteOrderById($order_ID);
	function deleteOrder( $orderId )
	{
		$api = getApi();
		$del = $api->rest( 'DELETE', '/admin/orders/'.$orderId.'.json' );

		if( $del->errors ) {

			echo "<br>[DELETE ORDER] Got {$del->errors->status} error.<br>";
			print_r( $del->errors->body );

		} else {
			echo "<br>[DELETE ORDER] $orderId was successfull.<br>";
		}
	}

	/*
	 * check email owner is a user and membership
	 */
	 function checkMembership( $email )
	 {
		 $tag = "membership";
		 $api = getApi();
		 $customer = $api->rest( 'GET', '/admin/customers/search.json?query='.$email.'&fields=email,id,tags' );

		 if( strpos( $customer->tag, $tag ) !== false ) {

			return true;

		} else {

			return false;
		}
	 }

}
