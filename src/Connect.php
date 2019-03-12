<?php
// require __DIR__ . './src/get_api.php';
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
        if (!isset(self::$instances[static::class])) {
            self::$instances[static::class] = new static();
        }
        return self::$instances[static::class];
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
        $this->config = $json;// json_decode($json);
    }
}

/***** Class Call Shopify API *****/
class CallApi extends Connect
{
    public $handler;

    protected function __construct()
    {
        $api = new BasicShopifyAPI(true);

        $config = Config::getInstance();
        $this->handler = array(
            $config->domain,
            $config->apikey,
            $config->secret
        );
    }
}
// Fill the config with some JSON
$config = Config::getInstance();
$config->setConfig(
    //'{"domain":"sarotestas.myshopify.com","apikey":"66037abc84c74449d27728102f16be37","secret":"29d74413a5e4dbffd586e63a7e97f850"}'
    array('domain'=>'sarotestas.myshopify.com','apikey'=>'66037abc84c74449d27728102f16be37','secret'=>'29d74413a5e4dbffd586e63a7e97f850')
);
// Init the db connection (which depends on the Config singleton)
$callApi = CallApi::getInstance();
// Get order info
$orderId = 949967028309;
$get = $api->rest('GET', '/admin/orders/'. $orderId .'.json');
