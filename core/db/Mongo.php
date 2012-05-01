<?php
namespace core\db;
use core\Core as Core;

/**
 * mongo db wrapper
 * @author hrodicus@gmail.com
 */
class Mongo
{
    
    /**
     * mongo collection instance
     * @var type 
     */
    public static $instance = null;
    /**
     *
     * @var object MongoDB 
     */
    public static $db = null;
    /**
     *
     * @var object MongoCollection
     */
    public static $collection = null;
    /**
     * already initialized?
     * @var bool 
     */
    public static $ready = false;
    
    /**
     *
     * @param type $config
     * @return type 
     */
    public static function init()
    {
        if(self::$ready)
            return;

        if(!isset(Core::$config['database']['mongo']))
            throw new Exception('missing database Mongo configuration!');
        
        $replica = false;
        if(Core::$config['database']['mongo']['replica'] === true)
            $replica = array('replicaSet' => true);

        self::$instance = new \Mongo(Core::$config['database']['mongo']['conn'], $replica);
        /*}
        catch (MongoConnectionException $e) {
            \core\Exception::handler($e);
        } catch (MongoException $e) {
            \core\Exception::handler($e);
        } catch (MongoCursorException $e) {
            die(__('Error: probably username password in config').$e->getMessage()); 
        }*/
        self::$ready = true;
    }
    
    /**
     *
     * @return object Mongo 
     */
    public static function getInstance()
    {
        self::init();
        return self::$instance;
    }
    
    /**
     *
     * @param type $dbName
     * @return MongoDB
     */
    public static function selectDB($dbName)
    {
        self::init();
        try
        {
            self::$db = self::$instance->selectDB($dbName);
            return self::$db;
        }
        catch ( MongoConnectionException $e )
        {
            \core\Exception::handler($e);
        }
    }
    
    /**
     *
     * @param type $dbName
     * @return MongoCollection
     */
    public static function selectCollection($collection)
    {
        self::init();
        return self::$instance->selectCollection(self::$db, $collection);
    }
    
}
    
