<?php
/**
 * core base file
 * @author hrodicus@gmail.com
 * 
 * the most important part of the app is here. take care of it!
 * memory usage of this framework: <1MB (wordpress ~40MB XD)
 */
namespace core;

/**
 * Framework core class
 */
class Core
{
	
	/**
	 * environment stages
	 */
	const PRODUCTION = 'production';
	const STAGING = 'staging';
	const TESTING = 'testing';
	const DEVELOPMENT = 'development';

    /**
     * environment site config
     * @var type 
     */
    public static $config = null;
    /**
     * request data
     * default controller and methods are set here
     * @var <array> 
     */
    public static $request = array(
        'controller'    => 'index',
        'method'        => 'index'
    );
    /** 
     * DEBUG_BENCHMARK = true
     * @var array 
     */
    public static $benchmark = array('app','db');
    /**
     * DEBUG_SYSTEM = true
     * @var array 
     */
    public static $debug = array();
    /**
     *
     * @param type $config 
     */
    public static function run($config)
    {         
        ob_start();
        
        /**
         * Start benchmarking
         */
        if(DEBUG_BENCHMARK)
        {
            self::$benchmark['app']['elapsed'] = microtime();
            self::$benchmark['app']['memory'] = memory_get_usage();
        }
        set_exception_handler(array('core\Exception', 'handler'));
        set_error_handler(array('core\Core', 'errorHandler'));
        register_shutdown_function(array('core\Core', 'shutdownHandler'));
        
        self::$config = $config;

        /**
         * Route and dispatch
         */
        self::route();
        $output =  self::dispatch(true);
        if($output === false)
            $output = self::dispatch(true);

        /**
         * End benchmarking
         */
        if(DEBUG_BENCHMARK)
        {
            self::$benchmark['app']['elapsed'] = round((microtime() - self::$benchmark['app']['elapsed']/1000)).'ms';
            self::$benchmark['app']['memory'] = round((memory_get_usage() - self::$benchmark['app']['memory']) / 1024).'KB';
            self::$benchmark['app']['memorypeak'] = (memory_get_peak_usage(true) / 1024).'KB';
        }
        
        if (Core::isAjaxRequest())
        {
            if(!is_array($output))
                throw new Exception('output should be an array in ajax requests!');

            header('Content-Type: text/json; charset=utf-8', TRUE, 200);
            // profiling / benchmark
            if(DEBUG_BENCHMARK)
                $output['benchmark'] = self::$benchmark;
            if(DEBUG_DB || DEBUG_QUERIES || DEBUG_CACHE)
                $output['debug'] = self::$debug;

            echo json_encode($output);
            ob_end_flush();
            session_commit();
            exit(0);
        }
        else
        {
            header('Content-Type: text/html; charset=utf-8', TRUE, 200);
            // profiling / benchmark
            if(DEBUG_BENCHMARK)
                $output.= "\n".'<!--Benchmark: '.json_encode(self::$benchmark).'-->';
            if(DEBUG_DB || DEBUG_QUERIES || DEBUG_CACHE)
                $output.= "\n".'<!--Debug: '.json_encode(self::$debug).'-->';

            echo $output;
            ob_end_flush();
            session_commit();
            exit(0);
        }
    }
    
    /**
     * resolve routing
     * @param string $uri 
     */
    public static function route($uri=null)
    {
        self::setParam('base', strtr(preg_replace('/\/[^\/]+$/','',$_SERVER['SCRIPT_NAME']),'\\','/'));        
        // if controller/method specified, no need to find a route
        if(!empty($_GET['c']))
        {
            self::setParam('controller', strtolower($_GET['c']));
            if(!empty($_GET['m']))
            {
                self::setParam('method', strtolower($_GET['m']));
            }
            return true; // routing no needed!
        }
        // specific uri as parameter or client request uri?
        if($uri === null)
        {
            $uri = $_SERVER['REQUEST_URI'];
        }
		krsort(self::$config['routes']);
		$req=preg_replace('/^'.preg_quote(self::getParam('base'),'/').
			'\b(.+)/','\1',rawurldecode($uri));
        // match
        $found = false;
        $args = array();
		foreach (self::$config['routes'] as $route=>$routeOptions) 
        {
			if (!preg_match('/^'.
				preg_replace(
					'/(?:{{)?@(\w+\b)(?:}})?/i',
					// Valid URL characters (RFC 1738)
					'(?P<\1>[\w\-\.!~\*\'"(),\s]+)',
					// Wildcard character in URI
					str_replace('\*','(.*)',preg_quote($route,'/'))
				).'\/?(?:\?.*)?$/i',$req,$args))
				continue;
            // args[0] is the requested uri that matched the route
            foreach ($args as $key=>$arg)
            {
                // Remove non-zero indexed elements
                if (is_numeric($key) && $key)
                    unset($args[$key]);
            }
            $found = true;
            break;
        }
        // route configuration wildcards are used as GET parameters
        $_GET = array_merge($_GET, $args); 
        if($found === false)
        {
            Session::setFlash('request', $_SERVER['REQUEST_URI']);
            self::setParam('controller', 'error');
            self::setParam('method', 'notFound404');
            return true;
        }
        
        // route options
        if(isset($routeOptions['c']))
            self::setParam('controller', $routeOptions['c']);
        if(isset($routeOptions['m']))
            self::setParam('method', $routeOptions['m']);
        if(isset($routeOptions['get']))
            $_GET = array_merge($_GET, $routeOptions['get']);
        if(isset($routeOptions['post']))
            $_POST = array_merge($_GET, $routeOptions['post']);
      
        return true;
    }
    
    /**
     *
     * @param type $return return content
     */
    public static function dispatch($return=false)
    {
        self::setParam('ipa', self::getIPAddress());
        
        try
        {
            // prepare class and method names
            $className = 'controller\\'.ucfirst(self::getParam('controller')).'Controller';
            if(!class_exists($className))
            {
                Session::setFlash('request', $_SERVER['REQUEST_URI']);
                self::setParam('controller', 'error');
                self::setParam('method', 'notFound404');
                return false;
            }
            $Controller = new $className();
            $Controller->preDispatch();
            $methodName = self::getParam('method');
            if(!is_callable(array($Controller, $methodName)))
            {
                Session::setFlash('request', $_SERVER['REQUEST_URI']);
                self::setParam('controller', 'error');
                self::setParam('method', 'notFound404');
                return false;
            }
            $Controller->$methodName();
            $Controller->postDispatch();
        }
        catch(\Exception $e)
        {
            Exception::handler($e);
        }
        return $Controller->output($return);
    }
    
    /**
     * get request parameter
     * @param type $key 
     */
    public static function getParam($key)
    {
        if(isset(self::$request[$key]))
            return self::$request[$key];
        return null;
    }
    
    /**
     *
     * @param type $key
     * @param type $value
     * @return type 
     */
    public static function setParam($key, $value)
    {
        self::$request[$key] = $value;
    }
    
    /**
     *
     * @return type 
     */
    public static function getIPAddress()
    {
        $ipa = '0.0.0.0';
        if (isset($_SERVER['REMOTE_ADDR']) AND isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $ipa = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['REMOTE_ADDR']))
        {
            $ipa = $_SERVER['REMOTE_ADDR'];
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $ipa = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ipa = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        return $ipa;
    }
    
    /**
     *
     * @param type $name
     * @param type $vars
     * @return type 
     */
    public static function view($name, $vars=false)
    {        
        if(is_array($vars))
            extract($vars);
        $fileName = realpath(APPLICATION_PATH . '/view/'.$name.'.php');
        if(!file_exists($fileName))
            throw new Exception("View file $name not found");
        ob_start();
        include $fileName;
        return ob_get_clean();
    }
    
    /**
     *
     * @param type $name
     * @param type $vars
     * @return type 
     */
    public static function layout($name, $vars=false)
    {
        if(is_array($vars))
            extract($vars);
        $fileName = realpath(APPLICATION_PATH . '/layout/'.$name.'.php');
        if(!file_exists($fileName))
            throw new Exception("Layout file $name not found");
        ob_start();
        include $fileName;
        return ob_get_clean();
    }
    
    /**
     * localized number format. Useful for price display
     * @param type $value
     * @param type $decimals
     * @return type 
     */
    public static function localeNumberFormat($value, $decimals=2)
    {
        return number_format($value, $decimals, self::$request['localeconv']['decimal_point'], self::$request['localeconv']['thousands_sep']);
    }

    /**
     * know if is an AJAX request
     * is posible to override the output by posting isAjaxLoadRequest parameter
     * @return type 
     */
    public static function isAjaxRequest()
    {
        if(isset($_POST['isAjaxLoadRequest']))
            return false;
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }

    /**
     * return wether the user is using a mobile device (or is forced)
     * @uses $_GET['device'], $_SERVER['HTTP_USER_AGENT']
     * @return string 
     */
    public static function isMobileAgent()
    {
        if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
            return true;
        } else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
            return true;
        } else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
            return true;
        } else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
            return true;
        }
        return false || (isset($_GET['device']) && $_GET['device'] == 'mobile');
    }
    
    /**
     *
     * @param type $class
     * @return type 
     */
    public static function autoLoad($val)
	{
        try
		{
            $val = str_replace('\\', '/', $val. '.php');
            // resolve using namespace
			if(substr_compare($val, 'core/', 0, 5) === 0)
            {
				$fileName = realpath(CORE_PATH . '/../'. $val);
			}
			else
			{
				$fileName = APPLICATION_PATH . '/'. $val;
			}
            if(DEBUG_SYSTEM)
                echo "<br />autoloading: $fileName";

            // load file
            if(file_exists($fileName))
            {
                include $fileName;
                return true;
            }
            return false;
		}
		catch (Exception $e)
		{
            Exception::handler($e);
			die;
		}
	}
    
    /**
	 * converts all errors into ErrorExceptions. 
     * This handler respects error_reporting settings.
	 *
	 * @throws  ErrorException
	 * @return  true
	 */
	public static function errorHandler($code, $error, $file = NULL, $line = NULL)
	{
		if (error_reporting() & $code)
		{
			throw new \ErrorException($error, $code, 0, $file, $line);
		}
		// Do not execute the PHP error handler
		return true;
	}
    
    /**
     * This is our shutdown function, in here we can do any last operations before the script is complete.
     */
    public static function shutdownHandler()
    {
        $error = error_get_last();
        if($error)
        {
            Exception::handler(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
            exit(1);
        }
    }

}