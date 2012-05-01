<?php
namespace core\db;
use core\Core as Core;

/**
 * mysql db wrapper
 * @author hrodicus@gmail.com
 */
class Mysql
{
    
    public static $master = null;
    public static $slaves = array();
    public static $ready = false;
    
    /**
     * 
     */
    public static function init()
    {      
        if(self::$ready)
            return;
        
        if(!isset(Core::$config['database']['mysql']))
            throw new Exception('missing database Mysql configuration!');

        if(!isset(Core::$config['database']['mysql']['master']))
            throw new Exception('missing mysql master configuration!');
        
        self::$master = new \PDO(
            Core::$config['database']['mysql']['master']['conn'], 
            Core::$config['database']['mysql']['master']['user'], 
            Core::$config['database']['mysql']['master']['pass'], 
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        ); 
        if(DEBUG_DB)
            self::$master->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        if(isset(Core::$config['database']['mysql']['slaves']))
        {
            foreach(Core::$config['database']['mysql']['slaves'] as $slave)
            {
                $slave = new \PDO(
                    $slave['conn'], 
                    $slave['user'], 
                    $slave['pass'], 
                    array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
                );  
                if(DEBUG_DB)
                    $slave->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$slaves[] = $slave;
            }
        }
        
        self::$ready = true;
    }
    
    /**
     * get master db instance
     * @return object PDO 
     */
    public static function getMaster()
    {
        self::init();
        return self::$master;
    }
    
    /**
     * get slave db instance
     * @return object PDO 
     */
    public static function getSlave()
    {
        self::init();
        $key = array_rand ( self::$slaves );
        return self::$slaves[$key];
    }
    
}
    