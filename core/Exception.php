<?php
namespace core;
/**
 * custom exceptions
 * @author hrodicus@gmail.com
 */
class Exception extends \Exception
{
    
    /**
	 * @var  array  PHP error code => human readable name
	 */
	public static $phpErrors = array(
		E_ERROR              => 'Fatal Error',
		E_USER_ERROR         => 'User Error',
		E_PARSE              => 'Parse Error',
		E_WARNING            => 'Warning',
		E_USER_WARNING       => 'User Warning',
		E_STRICT             => 'Strict',
		E_NOTICE             => 'Notice',
		E_RECOVERABLE_ERROR  => 'Recoverable Error',
	);
    /**
     * error page
     * @var type 
     */
	public static $errorView = '/view/error.php';
    
    public static function handler($e)
	{
		try
		{
            // Get the exception information
			$type    = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();

            // Create a text version of the exception
			$error = Exception::text($e);
            
            if($code !== E_NOTICE && $code !== E_STRICT)
            {  
                //log to file only important errors
                error_log($error);
            }
            
            // Get the exception backtrace
			$trace = $e->getTrace();

			if ($e instanceof \ErrorException)
			{
				if (isset(Exception::$phpErrors[$code]))
				{
					// Use the human-readable error name
					$code = Exception::$phpErrors[$code];
				}
			}
  
			if (PHP_SAPI=='cli')
			{
				// command line
				echo "\n{$error}\n";
				exit(1);
			}

			if ( ! headers_sent())
			{
				// Make sure the proper http header is sent
				$httpResponsecode = ($e instanceof HTTP_Exception) ? $code : 500;
				header('Content-Type: text/html; charset=utf-8', TRUE, $httpResponsecode);
			}

			if (Core::isAjaxRequest())
            {
                $httpResponsecode = 200;
				header('Content-Type: text/json; charset=utf-8', TRUE, $httpResponsecode);
                echo json_encode(array('error' => $error));
				exit(1);
			}

            ob_start();
			// Start an output buffer
			if (is_readable(realpath(__DIR__ . self::$errorView)))
			{
				include realpath(__DIR__ . self::$errorView);
			}
			else
			{
                throw new Exception('Error view file does not exist: '.self::$errorView);
			}
            echo ob_get_clean();
			// Display the contents of the output buffer
			exit(1);
		}
		catch (\Exception $e)
		{
            // Clean the output buffer if one exists
			ob_get_level() and ob_clean();
            // Display the exception text
			echo Exception::text($e), "\n";
			// Exit with an error status
			exit(1);
		}
	}
    
    /* 
	 * @param   object  Exception
	 * @return  string
	 */
	public static function text($e)
	{
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]', get_class($e), $e->getCode(), strip_tags($e->getMessage()), $e->getFile(), $e->getLine());
	}
    
}