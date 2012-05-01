<?php
namespace helper;
use core\Core as Core;

/**
 * Captcha helper
 *
 * @author hrodicus@gmail.com
 */
class Captcha
{
    
    /**
     *
     * @param type $return
     * @param type $error
     * @param type $ssl
     * @return type 
     */
    public static function recaptchaWidget($return=false, $error=false, $ssl=false)
    {
        if(!isset(Core::$config['thirdparty']['recaptcha']) || !isset(Core::$config['thirdparty']['recaptcha']['publicKey']))
        {
            throw new \ErrorException('missing recaptcha configuration parameters');
        }
        include_once(APPLICATION_PATH . '/thirdparty/recaptcha/recaptchalib.php');
        $content = recaptcha_get_html(Core::$config['thirdparty']['recaptcha']['publicKey'], $error, $ssl);
        if($return === true){
            return $content;
        }
        echo $content;
    }
    
    /**
     *
     * @param type $challenge
     * @param type $input
     * @return type 
     */
    public static function recaptchaValidate($challenge, $input)
    {
        if(!isset(Core::$config['thirdparty']['recaptcha']) || !isset(Core::$config['thirdparty']['recaptcha']['privateKey']))
        {
            throw new \ErrorException('missing recaptcha configuration parameters');
        }
        include_once(APPLICATION_PATH . '/thirdparty/recaptcha/recaptchalib.php');
        $r = recaptcha_check_answer(
            Core::$config['thirdparty']['recaptcha']['privateKey'],
            $_SERVER["REMOTE_ADDR"], $challenge, $input
        );
        if($r->is_valid){
            return true;
        }
        return $r->error;
    }
    
}