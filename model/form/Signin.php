<?php
namespace model\form;
use core\Form as Form;

/**
 * @author hrodicus@gmail.com
 */
class Signin extends Form
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
        'password'   => array(
            'required', 'regexp' => '(?=^.{6,16}$)(?=.*\w)(?!.*\s).*$'
        )
    );
    
    /**
     * extend base form validation, to check user and pass!
     */
    public static function validate()
    {
        if(parent::validate() === false)
            return false;
        
        // check user db
        $user = \model\Users::getByUserName(static::$valid['username']);
        if($user === null)
        {
            static::$errors[] = array('username' => _('User not found'));
            return false;
        }
        if(!isset($user['password']) || $user['password'] !== sha1(static::$valid['password']))
        {
            static::$errors[] = array('password' => _('Incorrect password'));
            return false;
        }
        return true;
    }
    
}