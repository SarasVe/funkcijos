<?php
namespace src;
include ('Order.php');

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 'On');
error_reporting(-1);

class Customer extends Order
{
    public function __construct(){
		// $this->checkMembership( 'vards@mail.com' );
	}

    /*
	 * check email owner is a user and membership
	 */
    function checkMembership( $email )
    {
        $tag = "membership";
        $api = Order::getApi();
        $query = array('query' => 'email:'. $email .'', 'fields' => 'tags, id' );

        try {
            $customer = $api->rest( 'GET', '/admin/customers/search.json', $query );
            // check API errors
            if ( $customer->errors ) {

                echo "Oops! {$customer->errors->status} error";
                print_r( $customer->errors->body );
            }
            $tags = reset( $customer->body->customers )->tags;

            if( strpos( $tags, $tag ) !== false ) {

                return true;

            } else {

               return false;
            }

        } catch (\Exception $e) {
            //print_r( $e );
            return false;
        }
    }
}
