<?php
// require __DIR__ . './src/get_api.php';
require_once __DIR__ . '/vendor/autoload.php';
use OhMyBrew\BasicShopifyAPI;

ini_set('display_errors', 'On');
error_reporting(-1);

abstract class Connect
{
    /**
     * @var Connect[] The reference to *Singleton* instances of any child class.
     */
    private static $instances = array();
    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return static The *Singleton* instance.
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        return self::$instances[$class];
    }
    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct() {}
    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    protected function __clone() {}
    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    protected function __wakeup() {}
}

/***** Class config for connections *****/
class Config extends Connect
{
    public $config;

    public function setConfig($json)
    {
        $this->config = json_decode($json); // $json;
    }
}

/***** Class Call Shopify API *****/
class CallApi extends Connect
{
    //public $handler;

    $domain = 'sarotestas.myshopify.com';
    $apiKey = '66037abc84c74449d27728102f16be37';
    $secret = '29d74413a5e4dbffd586e63a7e97f850';

    protected function __construct()
    {
        $api = new BasicShopifyAPI(true);

        $api->setShop($domain);
        $api->setApiKey($apiKey);
        $api->setApiPassword($secret);

        return $api;
        /*$config = Config::getInstance();
        $this->handler = array(
            'domain' => $config->domain, // ['domain'],
            'apikey' => $config->apikey, // ['apikey'],
            'secret' => $config->secret // ['secret']
        );*/
    }
}
// Fill the config with some JSON
/*$config = Config::getInstance();
$config->setConfig(
    '{"domain":"sarotestas.myshopify.com","apikey":"66037abc84c74449d27728102f16be37","secret":"29d74413a5e4dbffd586e63a7e97f850"}'
    //array('domain'=>'sarotestas.myshopify.com','apikey'=>'66037abc84c74449d27728102f16be37','secret'=>'29d74413a5e4dbffd586e63a7e97f850')
);*/
// Init the db connection (which depends on the Config singleton)
$call = CallApi::getInstance();
// Get order info
$orderId = 949967028309;
$get = $call->rest('GET', '/admin/orders/'. $orderId .'.json');
