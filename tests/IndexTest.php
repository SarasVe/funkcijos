<?php
require_once 'index.php';
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    var $abc;

    function OrderTest($abc)
    {
        $this->TestCase($abc);
    }

    function setUp()
    {
        // create a new instance of String with the
        // string 'abc'
        $this->abc = new Order();
    }
    function tearDown() {
        // delete your instance
        unset($this->abc);
    }

    public function testCheckString()
    {
        foreach ($this as $key => $value) {
            $this->assertNotNull($value);
        }
    }
}
/*
array(
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
);*/
