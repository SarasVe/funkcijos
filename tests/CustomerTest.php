<?php
namespace tests;

//include('./src/Order.php');
include('./src/Customer.php');

//use src\Order;
use src\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    var $test;

    function CustomerTest( $test )
    {
        $mail = 'vards@mail.com';
        $this->testCheckMembership( );
    }

    function setUp(): void
    {
        $this->test = new Customer(); // create a new instance of String with the string 'test'
    }

    public function testCheckMembership( )
    {
        $mail = 'vards@mail.com'; //'xxx'; //
        //echo "emailas: $mail";
        $return = $this->test->checkMembership( $mail );
        //print_r($return);
        //if( $return == false ) echo '\r\n FALSE \r\n';
        //if( $return == true ) echo '\r\n TRUE \r\n';
        $this->assertTrue($return);
    }

    function tearDown(): void
    {
        unset( $this->test ); // delete your instance
    }
}
