<?php
/**
 * production environment config file
 * CAUTION!!
 * @author hrodicus@gmail.com
 */

/**
 * execution config
 */
set_time_limit(10);
ini_set('display_errors', 0);
ini_set('memory_limit','16M');
error_reporting(0);

/**
 * debug, benchmarking...
 */
define('DEBUG_BENCHMARK', true);	// time and memory
define('DEBUG_DB', false);          // enable exceptions PDO
define('DEBUG_QUERIES', false);     // debug queries
define('DEBUG_CACHE', false);        // debug cache usage
define('DEBUG_SYSTEM', false);      // debug autoloading files and other problems

/**
 * absolute paths
 */
define('DOCUMENT_ROOT', '/var/www');    // path to apache doc root
define('CORE_PATH', DOCUMENT_ROOT . '/path/to/core');   //core framework path
define('APPLICATION_PATH', DOCUMENT_ROOT . '/path/to/app');  //app path

define('BASE_URL', 'http://www.your-domain.com');

/**
 * cache config
 * host, port, weight
 */
$config['memcache'] = array(
    'prefix'    =>  '',
    'servers'   =>  array(
        array('127.0.0.1', '11211', 100)
    )
);

/**
 * Database config
 * Important, you might configure Mysql and Mongo dbs at the same time,
 * be sure that you use them correctly (check session db config too!)
 */
$config['database'] = array(
    // if you want to use replica, set to True, and add the hosts in the conn string
    'mongo' => array(
        'conn'      => 'mongodb://root:1234@localhost:27017',
        'replica'   => false
    )
);

/**
 * session config
 */
ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', '127.0.0.1:11211');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1000);
$config['session'] = array(
    'db'            => 'mongo', //mongo or mysql available
    'name'          => 'dbname',
    'lifetime'      => intval(ini_get('session.gc_maxlifetime')),
    'refreshTime'   => 30 // time to refresh DB row
);

/**
 * email config
 */
$config['mail'] = array(
    'admin' =>  'postmaster@your-domain.com'
);

/**
 * thirdparty configs
 */
$config['thirdparty'] = array(
    'recaptcha' => array(  
        'publicKey'     => '',
        'privateKey'    => ''
    )
);

/**
 * Bundler configurations
 */
$config['bundle'] = true;
