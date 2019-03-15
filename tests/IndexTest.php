<?php
namespace tests;

include('./src/index.php');

use src\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    var $test;

    function OrderTest( $test )
    {
        $this->testCheckString( $test );
    }

    function setUp(): void
    {
        $this->test = new Order(); // create a new instance of String with the string 'test'
    }
    function tearDown(): void
    {
        unset( $this->test ); // delete your instance
    }

    /**
     * @dataProvider infoProvider
     */
    public function testCheckString( $d )
    {
        $check = new Order;
        $this->assertTrue( true == $check->checkString( $d ) );
    }

    public function infoProvider( $d )
    {
        //global $data, $d;
        //$data = json_decode( json_encode( $data ) );

        /*if( is_array( $data ) ) {
            $data = $this->arrayToObject( $data );
        }*/
        //$d = $data;

        //echo '<pre>'; var_dump( $d ); echo '</pre>';
        foreach( $d as $k => $v ) {

            $d->$k = "";
            $this->testCheckString( $d );
            $d->$k = $v;

            if( is_object( $v ) ) {

                $this->infoProvider( $v );
                /*foreach( $v as $k1 => $v1 ) {

                    $d->$k->$k1 = "";
                    $this->testCheckString( $d );
                    $d->$k->$k1 = $v1;

                    if( is_object( $v1 ) ) {

                        foreach( $v1 as $k2 => $v2 ) {

                            $d->$k->$k1->$k2 = "";
                            $this->testCheckString( $d );
                            $d->$k->$k1->$k2 = $v2;
                        }
                    }
                }*/
            }
        }
    }
}
// sample order data
/*$data = array(
    'order' => array(
        'name' => 'vardenis',
        'email' => 'vards@mail.com',
        'note' => 'Gateway: PayPal',
        'line_items' => array([
            'line_item_id' => 321312,
            'variant_id' => 238974,
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
);*/

// Simo ARRAY
$data = array (
	  'order' =>
	  array (
	    'line_items' => 'tusas',
	    'customer' =>
	    array (
	      'first_name' => 'vardas',
	      'last_name' => 'pavardas',
	      'email' => 'info@mail.com',
	    ),
	    'billing_address' =>
	    array (
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
	    'shipping_address' =>
	    array (
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
			'shipping_lines' =>
			array(
			0 =>
				array(
					'price' => 23.99,
					'title' => 'akiu tusas'
				)
			),
	    'transactions' =>
	    	array (
					0 =>
						array (
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
	);
//$testas = new OrderTest($data);
//$result = PHPUnit::run($testas);
//$test = OrderTest::testCheckString();
$data = json_decode( json_encode( $data ) );
$test = new OrderTest;
$test->infoProvider( $data );
//echo $test->testCheckString();
