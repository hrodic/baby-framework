<?php
namespace core;

/**
 * Memcache wrapper
 *
 * @author rodrigo
 */
class Cache {

    /**
     *
     * @var type 
     */
    protected static $instance;
    public static $ready;

    /**
    * Singleton to call from all other functions
    */
    public static function init()
    {
        if(self::$ready)
            return;
        if(Core::$config['memcache'] === null || Core::$config['memcache']['servers'] === null){
            throw new \ErrorException(__METHOD__ . ' expects array of servers', 500, E_ERROR);
        }
        if(self::$instance === null)
        {
            self::$instance = new \Memcached();
            if(isset(Core::$config['memcache']['prefix']))
            {
                self::$instance->setOption(\Memcached::OPT_PREFIX_KEY, Core::$config['memcache']['prefix']);
            }
            self::$instance->addServers(Core::$config['memcache']['servers']);
        }
        self::$ready = true;
        //clean memcached
        if(isset($_GET['rebuild']))
        {
            self::flush(0);
        }
    }
    
    /**
     *
     * @param type $name
     * @param type $arguments 
     */
    public static function __callStatic($name, $arguments) 
    {
        self::init();
        return call_user_func_array(array(self::$instance, $name), $arguments);
    }
    
    /**
     * Get key value based on caller data (__METHOD__ and func args)
     * @param type $method
     * @param type $args
     * @return type 
     */
    public static function getKeyByCaller($method, $args)
    {
        self::init();
        return sha1(json_encode(array_merge(array($method),$args)));
    }
    
}