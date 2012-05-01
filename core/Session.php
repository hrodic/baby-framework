<?php
namespace core;
/**
 * session wrapper
 * @author rodrigo
 */
class Session
{
    
    public static $start = false;
    public static $session_name = false;

    public static function init()
    {
        if(self::$start)
            return;
        
        register_shutdown_function('session_write_close'); 
        switch(Core::$config['session']['db'])
        {
            case 'mongo':
                session\Mongo::init();
                break;
            default:
                session\Mysql::init();
        }
        
        session_set_cookie_params(
            0,
            '/',
            null,
            isset($_SERVER["HTTPS"]),
            true
        ); 
        self::$session_name = Core::$config['session']['name'];
        session_name(self::$session_name);
        session_start();
        if(session_id() == "")
        {
            session_regenerate_id();
        }
        
        // session ready!
        self::$start = true;
    }
    
    /**
     * Cross-site request forgery
     */
    public static function checkCRSF()
    {
        if($_POST)
        {
            if(
                !self::get('csrfKey') || 
                self::get('csrfKey') !== $_POST['csrfKey']
            ) die('CSRF detected. Incident reported.');
        }
    }
    
    /**
     *
     * @param type $key
     * @param type $value 
     */
    public static function set($key, $value)
    {
        self::init();
        $_SESSION[self::$session_name][$key] = $value;
    }
    
    /**
     *
     * @param type $key 
     */
    public static function get($key)
    {
        self::init();
        if(!isset($_SESSION[self::$session_name][$key]))
        {
            return null;
        }
        return $_SESSION[self::$session_name][$key];
    }
    
    /**
     *
     * @param type $key 
     */
    public static function remove($key)
    {
        self::init();
        $_SESSION[self::$session_name][$key] = null;
        unset($_SESSION[self::$session_name][$key]);
    }
    
    /**
     * Flash values will be removed after the first getFlash() usage
     * @param type $key
     * @param type $value 
     */
    public static function setFlash($key, $value)
    {
        self::init();
        if(!isset($_SESSION['flash']))
        {
            $_SESSION['flash'] = array();
        }
        $_SESSION['flash'][$key] = $value;
    }
    
    /**
     * One-time read key
     * @param type $key
     * @return type 
     */
    public static function getFlash($key)
    {
        self::init();
        if(!isset($_SESSION['flash']))
        {
            $_SESSION['flash'] = array();
        }
        if(!isset($_SESSION['flash'][$key]))
        {
            return null;
        }
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    
}