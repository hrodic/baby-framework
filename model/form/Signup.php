<?php
namespace model\form;
use core\Form as Form;
use core\Core as Core;

/**
 * @author hrodicus@gmail.com
 */
class Signup extends Form
{
      
    protected static $valid = array();
    protected static $errors = array();
    /**
     *
     * @var type 
     */
    protected static $rules = array(
        'username'      => array(
            'required', 'regexp' => '(?=^.{3,16}$)(?=.*\w)(?!.*\s).*$'
        ),
        'email'      => array(
            'required', 'filter' => FILTER_VALIDATE_EMAIL
        ),
        'password'   => array(
            'required', 'regexp' => '(?=^.{6,16}$)(?=.*\w)(?!.*\s).*$'
        ),
        're-password'  => array(
            'required', 'field-match' => 'password'
        ),
        'locale'     => array(
            'required', 'list'  => array('es_ES', 'en_US')
        ),
        't&c'      => array(
            'required', 'regexp' => 'on'
        ),
        'recaptcha_response_field'  => array(
            'required', 'captcha' => 'recaptcha_challenge_field'
        )
    );
    
    /**
     * this validates uses captcha, whos not in the core, so we extend parents validator
     * @return type 
     */
    public static function validate()
    {
        parent::validate();
        foreach(static::getRules() as $field => $rules)
        {
            if(array_key_exists('captcha', $rules))
            {
                $captchaResponse = \helper\Captcha::recaptchaValidate($_POST[$rules['captcha']], $_POST[$field]);
                // if captcha error...
                if($captchaResponse !== true)
                {
                    static::$errors[] = array($field => _('Incorrect captcha solution'));
                }
            }
        }
        return (0 === count(static::$errors));
    }
    
    /**
     *
     * @return type 
     */
    public static function sendConfirmationMail()
    {
        $activationHash = base64_encode(self::getValid('username').self::getValid('email'));
        $subject = _('welcome to xxxx ').self::getValid('username');
        $body = _('You just created a new account! Welcome ').'<strong>'.self::getValid('username').'</strong>';
        $body.= '<a href="http://'.$_SERVER['SERVER_NAME'].'/account/activation/'.$activationHash.'" target="_blank">Activate</a>';
        return \core\Mail::send(Core::$config['mail']['admin'], self::getValid('email'), $subject, $body);
    }
    
}
