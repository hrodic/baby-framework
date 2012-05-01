<?php
namespace model;
use core\model\Mongo as Mongo;
/**
 * schema
 */
class Users extends Mongo
{

    const EMAIL         = '_id';
    const USERNAME      = 'username';
    const PASSWORD      = 'password';
    const DATE_CREATION = 'date_creation';
    const STATUS        = 'status';
    const LANGUAGE		= 'language';
    const COUNTRY		= 'country';
    
    /**
     *
     * @param type $name
     * @return type 
     */
    public static function getByEmail($email)
    {
        return self::selectDB('db')->selectCollection('col')
                ->findOne(array(self::EMAIL => strtolower($email)));
    }
    
    /**
     * get user data
     * @param type $userName
     * @param type $fields
     * @return type 
     */
    public static function getByUserName($userName, $fields=array())
    {
        return self::selectDB('db')->selectCollection('col')
                ->findOne(array(self::USERNAME => strtolower($userName)), $fields);
    }
    
    /**
     * insert or update if doesnt exist
     * @param type $data
     * @param type $safe = true
     * @return type 
     */
    public static function upsert($criteria, $data, $safe=true)
    {       
        $r = self::selectDB('db')->selectCollection('col')
                ->update($criteria, $data, array('upsert' => true, 'safe' => $safe));
        return ($r['ok'] == 1);
    }
    
    /**
    *
    * @param type $data
    * @param type $safe 
    */
    public static function insert($data, $safe=true)
    {       
        $r = self::selectDB('db')->selectCollection('col')
                ->insert($data, array('safe' => $safe));
        return ($r['ok'] == 1);
    }
    
}
