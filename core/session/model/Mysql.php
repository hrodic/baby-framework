<?php
namespace core\session\model;

/**
 * sessions mysql model
 * @author hrodicus@gmail.com
 */
class Mysql extends \core\model\Mysql
{
    
    protected static $table = 'sessions.memcached';
    
    /**
     *
     * @param type $sessionId
     * @return type 
     */
    public static function getById($sessionId)
    {
        $sqlQuery = '
            SELECT * FROM '.self::$table.'
            WHERE sessionId = :sessionId'
        ;
        self::$stmt = self::getMaster()->prepare($sqlQuery, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        self::$stmt->bindParam(':sessionId', $sessionId, \PDO::PARAM_STR);
        self::execute();
        return self::$stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     *
     * @param type $sessionId
     * @param type $expiration
     * @return type 
     */
    public static function updateExpirationById($sessionId, $expiration)
    {
        $sqlQuery = '
            UPDATE '.self::$table.'
            SET expiration = :expiration
            WHERE sessionId = :sessionId
        ';
        self::$stmt = self::getMaster()->prepare($sqlQuery, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        self::$stmt->bindParam(':sessionId', $sessionId, \PDO::PARAM_STR);
        self::$stmt->bindParam(':expiration', $expiration, \PDO::PARAM_STR);
        self::execute();
        return self::$stmt->rowCount();
    }
    
    /**
     *
     * @param type $sessionId
     * @param type $expiration
     * @param type $data 
     */
    public static function replace($sessionId, $expiration, $data)
    { 
        $sqlQuery = '
            REPLACE INTO '.self::$table.' (sessionId, expiration, data) 
            VALUES (:sessionId, :expiration, :data)
        ';
        self::$stmt = self::getMaster()->prepare($sqlQuery, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        self::$stmt->bindParam(':sessionId', $sessionId, \PDO::PARAM_STR);
        self::$stmt->bindParam(':expiration', $expiration, \PDO::PARAM_STR);
        self::$stmt->bindParam(':data', $data, \PDO::PARAM_STR);
        self::execute();
        return self::$stmt->rowCount();
    }
    
    /**
     *
     * @param type $sessionId
     * @return type 
     */
    public static function deleteById($sessionId)
    {
        $sqlQuery = 'DELETE FROM '.self::$table.' WHERE sessionId = :sessionId';
        self::$stmt = self::getMaster()->prepare($sqlQuery, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        self::$stmt->bindParam(':sessionId', $sessionId, \PDO::PARAM_STR);
        self::execute();
        return self::$stmt->rowCount();
    }
    
    /**
     *
     * @return type 
     */
    public static function deleteExpired()
    {
        $sqlQuery = 'DELETE FROM '.self::$table.' WHERE expiration < NOW();';
        self::$stmt = self::getMaster()->prepare($sqlQuery, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        self::execute();
        return self::$stmt->rowCount();
    }
}