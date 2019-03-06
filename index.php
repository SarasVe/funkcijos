<?php

// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';
//require_once __DIR__ . '/view/main_view.php';
//require_once __DIR__ . '/view/products.php';

use OhMyBrew\BasicShopifyAPI;

ini_set('display_errors', 'On');
error_reporting(-1);

/*
 * Settings
 */
$domain = 'sarotestas.myshopify.com';
$apiKey = '66037abc84c74449d27728102f16be37';
$secret = '29d74413a5e4dbffd586e63a7e97f850';

// URL
$url = "https://{$apiKey}:{$secret}@{$domain}";

$api = new BasicShopifyAPI(true); //  true sets it to private
$api->setShop($domain);
$api->setApiKey($apiKey);
$api->setApiPassword($secret);

/*
 * import order, kuri pagal duota array sukuria nauja orderi
 * su basic informacija - vardas, adresas, email, ka pirko (line_items)
 * ir koki nors note pvz. Gateway: PayPal
 */

$order_ID = 949627715669; // 1002; 949627715669, 947864436821
$lineItem_ID = 321312;
$variant_ID = 238974;

// new order array
$newOrder = array(
	'order' => array(
		'id' => 947864436666,
		'name' => 'vardenis',
		'email' => 'vards@mail.com',
		'note' => 'Gateway: PayPal',
		'line_items' => array(array(
			'line_item_id' => $lineItem_ID,
			'variant_id' => $variant_ID,
			'title' => 't-shirt crazy',
			'quantity' => 1,
			'price' => 3.00
		))
	)
);

//importOrder($newOrder, $api);

function importOrder($arr, $api)
{
	if(isset($api) && isset($arr)){

		$call = $api->rest('POST', '/admin/orders.json', $arr); //  $orderData

		if ($call->errors) {

		  echo "<br>Oops! {$call->errors->status} error.<br>";

		  print_r($call->errors->body);
	  } else {
		  echo "<br>New order placed successfully!<br>";
	  }
	} else {

		echo "<br>Opps! variables are empy!<br>";
		echo "<br>Create new order failed<br>";
	}
}
/*/
/*
 * import upsell, argumentai jos order_id ir upsell items.
 * Ji turetu pasiimti originalaus orderio info pagal order_id,
 * visa ja issisaugot i kintamaji, istrinti originalu orderi,
 * ir sukurti nauja su ta pacia info, tik plius papildomai nauji upsell items.
 */

// upsell items array
$newItems = array([
		'variant_id' => 1111,
		'title'	=> 't-shirt fun',
		'quantity'  => 1,
		'price' => 4.00
	],[
		'variant_id' => 2222,
		'title'	=> 't-shirt fun',
		'quantity'  => 1,
		'price' => 2.00
	]
);

importUpsell($order_ID, $newItems, $api);

function importUpsell($orderID, $upsellItems, $api)
{
	$order = getOrderById($orderID, $api);

	$allItems = addItems($order, $upsellItems);

	$arr['order'] = $allItems;

	echo '<pre>'; var_dump($arr); echo '</pre>';

	importOrder($arr, $api);

	//deleteOrderById($orderID, $api);
}

//addItems(getOrderById($order_ID, $api), $a);
// add old and new list_items
function addItems($obj, $newData) // $list, $a)
{
	//$ob = json_decode($obj);
	//echo "<br>". $ob ."<br>";
	/*foreach ($obj as $k => $v) {
		$data[$k][$v] = $obj->$k->$v;
		print($data[$k][$v]);//->$k;
	}
	//print($obj);//order->//->line_items);
	//$data = (array) $obj;
	//var_dump($data);
	/*$items = $data->order; //line_items;
	$count=is_array($items) ? count($items) : 1;
	print $count;*/

	//$data = $list; //getOrderInfo($order_id);
	//$newData = $a;

	//var_dump($obj->body->order->line_items);
	$oldData = json_decode(json_encode($obj), true);

	// *** get old items and place to array
	$getOldItems = $obj->body->order->line_items;
	$oldItems = json_decode(json_encode($getOldItems), true);
	//var_dump($oldItems);
	$getOldOrder = $obj->body->order;
	$oldOrder = json_decode(json_encode($getOldOrder), true);
	//var_dump($oldOrder); echo "<br>line breakas<br>";
	$body = $obj->body;
	$body = json_decode(json_encode($body), true);
	//var_dump($body);
	/* item counter nebereikalingas, nes line_items jau yra sudeti i $oldItems
	$items = 0;
	foreach ($data as $key => $value) {
		$items++;
	}
	echo "<br>". $items ."<br>";
	*/
	//$newItems = array_merge( $oldItems, $newData );
	//$count = count($oldItems);
	//echo "<br>{$count}<br>";
	//var_dump($newItems);

	$key = array_search( "line_items", array_keys($oldOrder), true );
	if(isset($key))	echo "<br>line_items start at key: ".$key."<br>";
	else echo "<br>cant find line_items in body<br>";
	/*
	for ($i = 0; $i < count($data); $i++)
	  $products[$i] = $data[$i];*/
	//var_dump($newData);
	/*** ### Simo ### ***/
	/*for ($i = 0; $i < count($newData); $i++) {
	  $new = array(
	      'variant_id' => $newData[$i]['variant_id'],
	      'quantity' => $newData[$i]['quantity'],
	      'price' => $newData[$i]['price']
	  );
	  $newOrder = array_push($oldData, $new);
  }*/
	array_splice( $oldOrder, $key, 0, $newData ); // $newItems
	//var_dump($oldOrder);
	return $oldOrder;

  /*
  $newOrder = array_push($oldOrder, $oldItems);
  print_r($newOrder);
  return $newOrder;*/

	//$list1 = json_decode($list);
	//var_dump($list1)
	//$list2 = array();
	/*
	foreach($list as $k => $v){ // object to array error
		$list2[$k] = $list->$k;
		$list2[$v] = $list->$v;
	}
	//var_dump($list2);
	if(isset($list1) && isset($a)) {

		echo "<br>arrays passed to the function getOrderById.<br>";
		//$temp = array();
		/*$i = 0;
		foreach ($list as $key => $value) {
			// code...

			if($key == 'line_items'){
				$index = $i;
			}
			$i++;
		}
		$arrayObject = new ArrayObject(array($list1));

		if(isset($arrayObject)){

			echo "<br>new ArrayObject is created!<br>";
		}
		try {

			//$ar = $arrayObject->offsetGet('line_items');
			$new = $arrayObject->offsetSet("line_items", $a); // $ar

		} catch (\Exception $e) {

			echo "<br>".$e."<br>";
		}

		//var_dump($arrayObject);
		var_dump($new);

		foreach($list1 as $key => $value) {

			//$temp[$key] = $key;
			//print($list1);

			if($key == 'line_items') {

				echo $key;
				//foreach($a as $k => $v) {
				/*foreach($list1 as $k => $v) {
					//$temp[][$k] = $a[$k];
					echo $list1->$k;//->$v;
				}
				//$temp[$key] = $a;
			}
		}
	} else {

		echo "Variable 'list' is empty!";
	}*/
}

//getOrderById($order_ID, $api);//$url);
/*
 * get order by ID function
 */
function getOrderById($orderId, $api) //$url)
{
	try {

		$order = $api->rest('GET', '/admin/orders/'. $orderId .'.json');

		echo "<br>Read order $orderId info successfull.<br>";
		//var_dump($order->body->order);
		return $order;

	} catch (\Exception $e) {

		echo "<br>Failed to retrieve JSON of order $orderId.<br>";

		print($e);
	}
}
/*
 * get all $orders
 */
//getAllOrders($api);

function getAllOrders($api)
{
	//$orders = "{$url}/admin/orders.json";
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

function deleteOrderById($orderId, $api)
{
	try {
		$api->rest('DELETE', '/admin/orders/'.$orderId.'.json');

		echo "<br>Order deleted successfully.<br>";

	} catch (\Exception $e) {

		echo "<br>Order delete failed.<br>";
		echo $e;
	}
}

// Now run your requests...
//$api->rest(string $type, string $path, array $params = null);
/*
$result = $api->graph('{ shop { name } }'); // rest('GET', '/admin/shop.json');

try {
	echo $result->body->shop->name; // country;
	// catch nerodo klaidos
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
*/
/*****
$call = $api->rest('GET', '/admin/products/count.json');
if ($call->errors) {
  echo "Oops! {$call->errors->status} error <br>";
  print_r($call->errors->body);

  // Original exception can be accessed via `$call->errors->exception`
  // Example, if response body was `{"error": "Not found"}`...
  /// then: `$call->errors->body->error` would return "Not Found"
}
echo nl2br("Number of products in shop: {$call->body->count}.\n");
var_dump($call);
******/

function importUpsell($order_id, $newData = array(), $transaction_id = null, $price, $pp = null)
{
	global $config, $api;

	if(!isset($order_id) || !isset($newData) || !isset($price))
	{
		return false;
	}

	$data = getOrderInfo($order_id);

	$data = json_decode(json_encode($data), true);

	for ($i = 0; $i < count($data['order']['line_items']); $i++)
		$products[$i] = $data['order']['line_items'][$i];

	$sup = $data['order'];

	for ($i = 0; $i < count($newData); $i++) {
		$new = array(
				'variant_id' => $newData[$i]['variant_id'],
				'quantity' => $newData[$i]['quantity'],
				'price' => $newData[$i]['price']
		);
		array_push($products, $new);
	}

	$transactions = $api->rest( 'GET', '/admin/orders/'.trim($order_id).'/transactions.json' );
	$transactions = $transactions->body;
	$currentTransactions = json_decode(json_encode($transactions), true);

	$count;
	for ($i = 0; $i < count($currentTransactions['transactions']); $i++)
	{
		unset($currentTransactions['transactions'][$i]['id']);
		unset($currentTransactions['transactions'][$i]['order_id']);
		unset($currentTransactions['transactions'][$i]['status']);
		unset($currentTransactions['transactions'][$i]['message']);
		unset($currentTransactions['transactions'][$i]['created_at']);
		unset($currentTransactions['transactions'][$i]['test']);
		unset($currentTransactions['transactions'][$i]['authorization']);
		unset($currentTransactions['transactions'][$i]['currency']);
		unset($currentTransactions['transactions'][$i]['location_id']);
		unset($currentTransactions['transactions'][$i]['parent_id']);
		unset($currentTransactions['transactions'][$i]['user_id']);
		unset($currentTransactions['transactions'][$i]['device_id']);
		unset($currentTransactions['transactions'][$i]['receipt']);
		unset($currentTransactions['transactions'][$i]['error_code']);
		unset($currentTransactions['transactions'][$i]['source_name']);
		unset($currentTransactions['transactions'][$i]['admin_graphql_api_id']);
		unset($currentTransactions['transactions'][$i]['processed_at']);
		if($currentTransactions['transactions'][$i]['kind'] !== 'refund')
			$listTransactions[$i] = $currentTransactions['transactions'][$i];

		$count=$i;
	}

	$listTransactions[$count+1] = array(
	        'kind' => 'sale',
	        'status' => 'success',
	        'amount' => $price,
	        'gateway' => $transaction_id
	);

	deleteOrder($order_id);

	$data = array (
	  'order' =>
	  array (
	    'line_items' =>
		    $products,
	    'customer' =>
	    array (
	      'first_name' => $sup['customer']['first_name'],
	      'last_name' => $sup['customer']['last_name'],
	      'email' => $sup['customer']['email'],
	    ),
	    'billing_address' =>
	    array (
	      'first_name' => $sup['billing_address']['first_name'],
	      'last_name' => $sup['billing_address']['last_name'],
	      'address1' => $sup['billing_address']['address1'],
	      'phone' => $sup['billing_address']['phone'],
	      'city' => $sup['billing_address']['city'],
	      'province' => $sup['billing_address']['province'],
	      'country' => $sup['billing_address']['country'],
	      'zip' => $sup['billing_address']['zip'],
	    ),
	    'shipping_address' =>
	    array (
	      'first_name' => $sup['shipping_address']['first_name'],
	      'last_name' => $sup['shipping_address']['last_name'],
	      'address1' => $sup['shipping_address']['address1'],
	      'phone' => $sup['shipping_address']['phone'],
	      'city' => $sup['shipping_address']['city'],
	      'province' => $sup['shipping_address']['province'],
	      'country' => $sup['shipping_address']['country'],
	      'zip' => $sup['shipping_address']['zip'],
	    ),
	    'email' => $sup['email'],
	    'financial_status' => 'paid',
			'currency' => $sup['currency'],
			'suppress_notifications' => true,
	  ),
	);
 	$data['order']['transactions'] = $listTransactions;

	try {
		$respond = $api->rest( 'POST', '/admin/orders.json', $data );
		$respond = $respond->body;
	} catch (Exception $e) {
		logError('[IMPORT UPSELL] Caught exception: ' .  $e->getMessage());

		return false;
	}

	if(isset($sup['note_attributes'][0]))
	{
		$newArr = array();

		foreach($sup['note_attributes'] as $key=>$attribute)
		{
			$newArr[$attribute['name']] = $attribute['value'];
		}

		$newNotes = array(
			'order' =>
				array(
					'id' => $respond->order->id,
					'note_attributes' => $newArr
				)
		);

		addNoteToOrder($respond->order->id, $newNotes);
	}

	return $respond->order->id;

}
