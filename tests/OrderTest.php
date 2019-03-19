<?php
namespace tests;

include('./src/Order.php');

use src\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
/*    var $test;

    function OrderTest( $test )
    {
        $this->testCheckString( $test );
    }

    function setUp(): void
    {
        $this->test = new Order(); // create a new instance of String with the string 'test'
    }
*/

    /**
     * @dataProvider infoProvider
     */
    public function testCheckString( $d, $e )
    {
        $check = new Order; //var_dump( $d );
        $ats = $check->checkString( $d ); //error_log( print_r( $ats, true ) );
        //echo '$ats = ';var_dump($ats.'. '); echo '$e = ';var_dump($e.'. ');
        if( empty( $ats ) ) print_r(' Negryzo $ats is checkString. ');
        else {
          if( $ats == 'ok' ) $e = 'ok';
          $this->assertEquals( $e, $ats );
        }
    }

    public function infoProvider( $d )
    {
        //echo ' IKI FOREACH. ';
        print_r($d);
        if( $d->order ) $d = $d->order;
        //var_dump( $d );
        foreach( $d as $k => $v ) {

            //if( $k == 'order' ) $d = $d->order;
            //else {
                if( is_object( $v ) ) {

                    $this->infoProvider( $k );
                }
                if( $k == 'email' ) {

                  $d->$k = '123';
                  $e = $k; print_r( '\r\n Testuoju email. \r\n' );
                  $this->testCheckString( $d, $e );

                } else {

                $e = $k; //echo ' $k yra: '.$e.'. ';
                $d->$k = "";
                echo ' VIDUJE FOREACH. '; print_r($d->$k);
                $this->testCheckString( $d, $e );
                $d->$k = $v;
                }
            //}
       }
    }

/*
    function tearDown(): void
    {
        unset( $this->test ); // delete your instance
    }*/
}
// sample order data
$data = array(
    'order' => array(
        'name' => 'vardenis',
        'email' => 'vards@mail.com',
        'note' => 'Gateway: PayPal',
        'line_items' => array([
            //'line_item_id' => 321312,
            //'variant_id' => 238974,
            'title' => 't-shirt crazy',
            'quantity' => 1,
            'price' => 3.00,
        ]),
        'shipping_address' => array(
            'last_name' => 'BubLastName', //required
            'address1' => '123 Ship Street',  //required
            'phone' => '+37066655544',
            'city' => 'Shipsville',  //required
            'country' => 'France',
            'zip' => '41000',  //required
        )
    )
);

// Simo ARRAY
/*$data = array(
	  'order' => array (
	    'line_items' => 'tusas',
	    'customer' => array (
	      'first_name' => 'vardas',
	      'last_name' => 'pavardas',
	      'email' => 'info@mail.com',
	    ),
	    'billing_address' => array (
	      'first_name' => 'vardas',
	      'last_name' => 'pavardas',
	      'address1' => '12 bezdodnys',
				'address2' => '16 vilnius',
	      'phone' => 87612876,
	      'city' => 'kaunas',
	      'province' => 'kauno',
	      'country' => 'lietuva',
	      'zip' => 23423,
	    ),
	    'shipping_address' => array (
	      'first_name' => 'vardas',
	      'last_name' => 'pavardas',
	      'address1' => '12 bezdonys',
				'address2' => '16 vilnius',
	      'phone' => 87612876,
	      'city' => 'kaunas',
	      'province' => 'kauno',
	      'country' => 'lietuva',
	      'zip' => 124312,
	    ),
	    'email' => 'info@mail.com',
			'shipping_lines' => array(
			  0 => array(
					'price' => 23.99,
					'title' => 'akiu tusas'
				)
			),
	    'transactions' => array (
					0 => array (
					        'kind' => 'sale',
					        'status' => 'success',
					        'amount' => 50.00,
					        'gateway' => 'gateway: PayPal'
							),
			),
	    'financial_status' => 'paid',
	    'currency' => 'US baksai',
			'send_receipt' => false,
			'suppress_notifications' => true,
			'test' => 'testas'
	  ),
	);*/
$data = json_decode( json_encode( $data ) );
//if(!is_object($data)) echo "<br>nepaverte i objekta<br>";
$test = new OrderTest;
$test->infoProvider( $data );
//$email = 'vards@mail.com';
//$test->testCheckMembership( $email );
