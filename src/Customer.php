<?php
namespace src;
include ('Order.php');

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 'On');
error_reporting(-1);

class Customer extends Order
{
    public function __construct(){
		$this->checkMembership( 'vards@mail.com' );
	}

    /*
	 * check email owner is a user and membership
	 */
    function checkMembership( $email )
    {
        $tag = "membership";
        $api = Order::getApi();
        $query = array('query' => 'email:'. $email .'', 'fields' => 'tags, id' );
        $customer = $api->rest('GET', '/admin/customers/search.json', $query);

        $tags = reset($customer->body->customers)->tags; //print_r($tags);
        //$customer = json_decode( json_encode( $customer ), true );
        //print_r( reset($customer->body->customers)->tags );
        //print_r($customer);

        if( strpos( $tags, $tag ) !== false ) {

            return true;

       } else {

           return false;
       }
    }
}
