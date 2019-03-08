<?php

require_once __DIR__ . '/vendor/autoload.php';

use OhMyBrew\BasicShopifyAPI;

ini_set('display_errors', 'On');
error_reporting(-1);

/*
 * Settings
 */
$domain = 'sarotestas.myshopify.com';
$apiKey = '66037abc84c74449d27728102f16be37';
$secret = '29d74413a5e4dbffd586e63a7e97f850';

$api = new BasicShopifyAPI(true); //  true sets it to private
$api->setShop($domain);
$api->setApiKey($apiKey);
$api->setApiPassword($secret);

$order_ID = 949725298773;
$new_order_id = 949725298771;
$lineItem_ID = 321312;
$variant_ID = 238974;
$price = 5.00;

// new order array
$newOrder = array(
	'order' => array(
		//'id' => 947864436666,
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

importUpsell($order_ID, $newOrder, 0, $price, 0);

function importUpsell($order_id, $newData = array(), $transaction_id = null, $price, $pp = null)
{
	global $config, $api;

	if(!isset($order_id) || !isset($newData) || !isset($price))
	{
		return false;
	}

	$data = getOrderInfo($order_id, $api);

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

	deleteOrder($order_id, $api);

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

/*
 * get order by ID function
 */
function getOrderInfo($orderId, $api) //$url)
{
	try {

		$order = $api->rest('GET', '/admin/orders/'. $orderId .'.json');
		//$order = $order->body;

		echo "<br>Read order $orderId info successfull.<br>";
		//var_dump($order->body->order);
		return $order;

	} catch (\Exception $e) {

		echo "<br>Failed to retrieve JSON of order $orderId.<br>";

		print($e);
	}
}

/*
 * delete order by ID function
 */

function deleteOrder($orderId, $api)
{
	try {
		$api->rest('DELETE', '/admin/orders/'.$orderId.'.json');

		echo "<br>Order deleted successfully.<br>";

	} catch (\Exception $e) {

		logError('[DELETE ORDER] Caught exception: ' .  $e->getMessage());
	}
}
