<?php
namespace helper;
use core\Core as Core;
use core\Session as Session;
/**
 * user account control
 */
class Account
{

    public static function isAnonymous()
    {   
        return (Session::get('username') === null);
    }
    
    public static function isUser()
    {
        return (Session::get('username') !== null);
    }
    
    public static function isAdmin()
    {
        
    }
    
    public static function userName()
    {
        return ucfirst(Session::get('username'));
    }
    
}