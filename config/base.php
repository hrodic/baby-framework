<?php
/**
 * base.php config file
 * @author hrodicus@gmail.com
 *
 * 
 * all common configuration parameters for all environments should be defined here
 * 
 * In each environment config file we define specific variables
 * example: ips, paths, urls, keys for apps, etc...
 * so we can develop in a totally different environment (server, paths, etc...)
 * and have another params in production. cool, isn't it?
 */
define('APPLICATION_ENV', getenv('APPLICATION_ENV'));
if(!defined('APPLICATION_ENV'))
{
	define('APPLICATION_ENV', 'production');
}
include APPLICATION_ENV . '.php';
include CORE_PATH . '/Core.php';
spl_autoload_register(array('core\Core', 'autoLoad'));

/**
 * routes
 * default controller and method: index
 * @example
 * '/@country/test' => array(           //route (widcards @, *)
        'c'    =>  'index',             //controller to call
        'm'    =>  'index'              //method to call
        'get'  =>  array('idCategory' => array('restaurant, beauty'))  //get parameters to set
    )
 */
$config['routes'] = array(
    '/'         => array(),
);
