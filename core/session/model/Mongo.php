<?php
namespace core\session\model;
/**
 * sessions mysql model
 * @author hrodicus@gmail.com
 */
class Mongo extends \core\model\Mongo
{
    
    /**
     *
     * @param type $sessionId
     * @return type 
     */
    public static function getById($sessionId)
    {
        
    }
    
    /**
     *
     * @param type $sessionId
     * @param type $expiration
     * @return type 
     */
    public static function updateExpirationById($sessionId, $expiration)
    {
        
    }
    
    /**
     *
     * @param type $sessionId
     * @param type $expiration
     * @param type $data 
     */
    public static function replace($sessionId, $expiration, $data)
    { 
        
    }
    
    /**
     *
     * @param type $sessionId
     * @return type 
     */
    public static function deleteById($sessionId)
    {
        
    }
    
    /**
     *
     * @return type 
     */
    public static function deleteExpired()
    {
        
    }
    
}