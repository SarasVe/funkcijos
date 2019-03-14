<?php
namespace tests;

include('./src/index.php');

use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    var $abc;

    function OrderTest($abc)
    {
        $this->testCheckString($abc);
    }

    function setUp(): void
    {
        // create a new instance of String with the
        // string 'abc'
        $this->abc = new Order();
    }
    function tearDown(): void
    {
        // delete your instance
        unset($this->abc);
    }

    /**
     * @dataProvider infoProvider
     */
    public function testCheckString($d)
    {
        $check = new Order();
        $this->assertTrue($d);
    }


    public function infoProvider($data)
    {
        global $data, $d;
        $data = json_decode(json_encode($data),true);
        $d = $data;

        foreach($d as $k => $v){

            if(is_array($v)){

                foreach($v as $k1 => $v1){

                    $d[$k][$k1] = null;
                    OrderTest::testCheckString($d);
                    $d = $data;

                    if(is_array($v1)){

                        foreach($v1 as $k2 => $v2){

                            $d[$k][$k1][$k2] = null;
                            OrderTest::testCheckString($d);
                            $d = $data;
                        }
                    }
                }
            }
        }
    }
}
$data = array(
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
);
//$testas = new OrderTest($data);
//$result = PHPUnit::run($testas);
//$test = OrderTest::testCheckString($data);
//$t = true;
$test = OrderTest::infoProvider($data);
//echo $test->testCheckString();
