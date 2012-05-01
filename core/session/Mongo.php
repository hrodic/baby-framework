<?php
namespace core\session;
use core\Core as Core;
use core\Cache as Cache;
use core\session\model\Mongo as SessionModelMongo;

/**
 * mongo session handler
 */
class Mongo
{
    
    protected static $sessionName=null;
    protected static $lifeTime=null;
    protected static $refreshTime=null;
    protected static $sessionData=null;
    protected static $memcachePrefix='sessid=';
    
    public static function init()
    {
        self::$lifeTime = Core::$config['session']['lifetime'];
        self::$refreshTime = Core::$config['session']['refreshTime'];
        session_set_save_handler(
            array(__CLASS__, 'open'),
            array(__CLASS__, 'close'),
            array(__CLASS__, 'read'),
            array(__CLASS__, 'write'),
            array(__CLASS__, 'destroy'),
            array(__CLASS__, 'gc')
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
     * Gets a global (across *all* machines) lock on the session
     *
     * @param string $sessionId session id
     */
    protected static function _lock($sessionId)
    {
        $remaining = 30000000; // 30 seconds timeout, 30Million microsecs
        $timeout = 5000; // 5000 microseconds (5 ms)

        do {
            try {
                $query  = array('_id' => $sessionId, 'lock' => 0);
                $update = array('$set' => array('lock' => 1));
                $options = array('safe' => true, 'upsert' => true);
                $result = SessionModelMongo::selectDB('feudalonline')->selectCollection('sessions')->update($query, $update, $options);
                if ($result['ok'] == 1) {
                    return true; 
                }
                
            } catch (MongoCursorException $e) {
                if (substr($e->getMessage(), 0, 26) != 'E11000 duplicate key error') {
                    throw $e;  // not a dup key?
                }
            }

            usleep($timeout);
            $remaining = $remaining - $timeout;

            // wait a little longer next time, 1 sec max wait
            $timeout = ($timeout < 1000000) ? $timeout * 2 : 1000000;

        } while ($remaining > 0);

        // aww shit. 
        throw new Exception('Could not get session lock');
    }

    /**
     * Reads the session from Mongo
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
            //the record could not be found in the Memcache, loading from db
            self::_lock($sessionId);
            $doc = SessionModelMongo::selectDB('feudalonline')->selectCollection('sessions')->findOne(array('_id' => $sessionId));
            if(isset($doc['d']))
                self::$sessionData = $doc['d'];
            
            if(self::$sessionData)
            {
                self::updateDbExpiration($sessionId, $now);
            }
        } 
        else 
        {
            //time of the expiration in the Memcache
            $expiration = Cache::get(self::$memcachePrefix.$sessionId);
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
        $query  = array('_id' => $sessionId, 'lock' => 0);
        $update = array('$set' => array('expire' => $expiration));
        $options = array('safe' => true);
        $updateResult = SessionModelMongo::selectDB('feudalonline')->selectCollection('sessions')->update($query, $update, $options);
        if(!$updateResult)
        {
            self::destroy($sessionId);
        }
        //we store the time of the new expiration into the Memcache
        return Cache::set(self::$memcachePrefix.$sessionId, $expiration, self::$lifeTime);  
    }
    
    /**
     * Writes the session data back to mongo
     * 
     * @param string $sessionId
     * @param string $data
     * @return bool
     */
    public static function write($sessionId, $data)
    {
        $now = time();
        $expiration = self::$lifeTime + $now;

        // store data into memcache
        $result = Cache::set($sessionId, $data, self::$lifeTime);
        // if data changed, update db
        if (self::$sessionData !== $data) 
        {
            $doc = array(
                '_id'       => $sessionId,
                'lock'      => 0,
                'd'         => $data,
                'expire'    => time() + intval(ini_get('session.gc_maxlifetime'))
            );
            $options = array('safe' => true);
            $result = SessionModelMongo::selectDB('feudalonline')->selectCollection('sessions')->update(array('_id' => $sessionId), $doc, $options);
            // store time to refresh db into memcache
            Cache::set(self::$memcachePrefix.$sessionId, $expiration, self::$lifeTime);
        }
        return $result;
    }

    /**
     * Destroy's the session
     *
     * @param string $sessionId
     * @return bool
     */
    public static function destroy($sessionId)
    {
        Cache::delete($sessionId); //content
        Cache::delete(self::$memcachePrefix.$sessionId); //expiration
        self::$sessionData=null;
        $result = SessionModelMongo::selectDB('feudalonline')->selectCollection('sessions')->remove(array('_id' => $sessionId), array('safe' => true));
        return ($result['ok'] == 1); 
    }

    /**
     * Triggers the garbage collector, we do this with a mongo
     * safe=false delete, as that will return immediately without
     * blocking php.
     *
     * Maybe it'll delete stuff, maybe it won't. The next GC
     * will get'em.... eventually :)
     *
     * @return bool
     */
    public static function gc($max)
    {
        $results = SessionModelMongo::selectDB('feudalonline')->selectCollection('sessions')->remove(
            //array('expire' => array('$lt' => time()))
            array('$or' => array('expire' => array('$lt' => time()), 'expire' => array('type' => 10)))    
        );
    }
}