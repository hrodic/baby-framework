<?php
/**
 * development environment config file
 * @author hrodicus@gmail.com
 */

/**
 * execution config
 */
set_time_limit(5);
ini_set('display_errors', 1);
ini_set('memory_limit','8M');
error_reporting(E_ALL);

/**
 * debug, benchmarking...
 */
define('DEBUG_BENCHMARK', true);    // time and memory
define('DEBUG_DB', true);           // enable exceptions PDO
define('DEBUG_QUERIES', true);      // debug queries
define('DEBUG_CACHE', true);        // debug cache usage
define('DEBUG_SYSTEM', false);       // debug autoloading files and other problems

/**
 * absolute paths
 */
define('DOCUMENT_ROOT', '/var/www');    // path to apache doc root
define('CORE_PATH', DOCUMENT_ROOT . '/path/to/core');   //core framework path
define('APPLICATION_PATH', DOCUMENT_ROOT . '/path/to/app');  //app path

define('BASE_URL', 'http://www.your-domain.local');

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
    'mysql' => array(
        'master' => array(
            'user'  => 'root',
            'pass'  => '1234',
            'conn'  => 'mysql:host=localhost;port=3306;dbname=yourdbname'
        ),
        /*'slaves' => array(
            array(
                'user'  => '',
                'pass'  => '',
                'conn'  => ''
            )
        )*/
    ),
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
ini_set('session.gc_divisor', 100);
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
$config['bundle'] = false;
