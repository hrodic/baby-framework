<?php
namespace core\model;
use core\Core as Core;

/**
 * Base model for mysql db
 *
 * @author rodrigo
 */
class Mysql extends \core\db\Mysql
{
    
    /**
     * @see http://www.php.net/manual/es/class.pdostatement.php
     * @var PDOStatement
     */
    public static $stmt;
    
    /**
     * Single point of execution and error handler
     * @param type $statement 
     */
    public static function execute()
    {
        if(DEBUG_QUERIES)
        {
            $start = microtime();
        }
        $r = self::$stmt->execute();
        if(DEBUG_QUERIES)
        {
            $elapsed = round(microtime() - $start, 4);
            isset(Core::$benchmark['db']['count']) ? Core::$benchmark['db']['count']++ : Core::$benchmark['db']['count'] = 1;
            isset(Core::$benchmark['db']['elapsed']) ? (Core::$benchmark['db']['elapsed']+$elapsed) : Core::$benchmark['db']['elapsed'] = $elapsed;
        }
        if(self::$stmt->errorCode() !== '00000')
        {
            trigger_error(json_encode(self::$stmt->errorInfo()));
        }
        return $r;
    }
    
}