<?php
namespace core\session;
use core\Core as Core;
use core\Cache as Cache;
/**
 * 
 */
class Mysql
{
    
    protected static $sessionName=null;
    protected static $lifeTime=null;
    protected static $refreshTime=null;
    protected static $sessionData=null;

    public static function init()
    {       
        self::$lifeTime = Core::$config['session']['lifetime'];
        self::$refreshTime = Core::$config['session']['refreshTime'];
        session_set_save_handler(
            array(__CLASS__,"open"),
            array(__CLASS__,"close"),
            array(__CLASS__,"read"),
            array(__CLASS__,"write"),
            array(__CLASS__,"destroy"),
            array(__CLASS__,"gc")
        );
    }
    
    /**
     * opening of the session - mandatory arguments won't be needed
     * we'll get the session id and load session data, if the session exists
     *
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public static function open($savePath, $sessionName)
    {
        self::$sessionName = $sessionName;     
        setcookie(self::$sessionName, session_id(), 0, '/'); 
        return true;
    }
 
    /**
     * closing the session
     *
     * @return bool
     */
    public static function close()
    {
        self::$lifeTime = null;
        self::$sessionData = null;
        return true;
    }
    
    /**
     * reading of the session data
     * if the data couldn't be found in the Memcache, we try to load it from the DB
     * we have to update the time of data expiration in the db using _updateDbExpiration()
     * the life time in Memcache is updated automatically by write operation
     *
     * @param string $sessionId
     * @return string
     */
    public static function read($sessionId)
    {
        $now = time();
        self::$sessionData = Cache::get($sessionId);
        if (self::$sessionData===false) 
        {
            //the record could not be found in the Memcache, loading from the db
            self::$sessionData = model\Mysql::getById($sessionId);
            if(self::$sessionData)
            {
                self::updateDbExpiration($sessionId, $now);
            }
        } 
        else 
        {
            //time of the expiration in the Memcache
            $expiration = Cache::get('db-'.$sessionId);
            if($expiration) 
            {
                //if we didn't write into the db for at least
                //$this->_refreshTime, we need to refresh the expiration time in the db
                if($now-self::$refreshTime > $expiration-self::$lifeTime) 
                {
                    self::updateDbExpiration($sessionId, $now);
                }
            } 
            else 
            {
                self::updateDbExpiration($sessionId);
            }
        }
        Cache::set($sessionId, self::$sessionData,  self::$lifeTime);
        return self::$sessionData;
    }
    
    /**
     * update of the expiration time of the db record
     *
     * @param string $sessionId
     * @param int $now UNIX timestamp
     */
    private static function updateDbExpiration($sessionId, $now=null)
    {
        if(!$now) {
            $now = time();
        }
        $expiration = self::$lifeTime + $now;
        if(! model\Mysql::updateExpirationById($sessionId, $expiration) )
        {
            self::destroy($sessionId);
        }
        //we store the time of the new expiration into the Memcache
        return Cache::set('db-'.$sessionId, $expiration, self::$lifeTime);  
    }
 
    /**
     * cache write - this is called when the script is about to finish, or when session_write_close() is called
     * data are written only when something has changed
     *
     * @param string $sessionId
     * @param string $data
     * @return bool
     */
    public static function write($sessionId, $data)
    {
        $now = time();
        $expiration = self::$lifeTime + $now;

        //we store data in the Memcache
        $result = Cache::set($sessionId, $data, self::$lifeTime);

        if (self::$sessionData !== $data) 
        {
            //we store time of the db record expiration in the Memcache
            model\Mysql::replace($sessionId, $expiration, $data);
            Cache::set('db-'.$sessionId, $expiration, self::$lifeTime);
        }
        return $result;
    }
 
    /**
     * destroy of the session
     *
     * @param string $sessionId
     * @return bool
     */
    public static function destroy($sessionId)
    {
        Cache::delete($sessionId);
        Cache::delete('db-'.$sessionId);
        model\Mysql::deleteById($sessionId);
        self::$sessionData=null;
        return true;
    }

    /**
     * called by the garbage collector
     *
     * @param int $maxlifetime
     * @return bool
     */
    public static function gc($maxlifetime)
    {
        model\Mysql::deleteExpired();
        return true;
    }

}