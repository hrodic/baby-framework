<?php
namespace core;
/**
 * Form
 *
 * @author hrodicus@gmail.com
 */
class Form 
{
    
    /**
     * to fill in child classes, the valid fields
     * @var type 
     */
    protected static $valid = array();
    /**
     * to fill in child classes, the errors occurred
     * @var type 
     */
    protected static $errors=array();
    /**
     * to fill in child classes, rules to validate
     * @example
     *  'email'      => array(
            'required', 'filter' => FILTER_VALIDATE_EMAIL
        ),
     * @example
     * 'password'   => array(
            'required', 'regexp' => '(?=^.{4,16}$)(?=.*\d)(?=.*\w)(?!.*\s).*$'
        ),
     * @var type 
     */
    protected static $rules=array();
    
    /**
     * validate form based in defined rules and return true if no errors found
     * or false otherwise
     * @return <bool>
     */
    public static function validate()
    {
        foreach(static::getRules() as $field => $rules)
        {
            // if required...
            if($rules[0] == 'required' && (!isset($_POST[$field]) || !strlen($_POST[$field])))
            {
                static::$errors[] = array($field => _('Field required'));
            }
            // if not set, and not required, ignore
            if(!isset($_POST[$field]))
                continue;
            // if set and filter...
            if(array_key_exists('filter', $rules))
            {
                if(filter_var($_POST[$field], $rules['filter']) === false)
                {
                    static::$errors[] = array($field => _('Invalid value'));
                }
            }
            // if set and regexp
            if(array_key_exists('regexp', $rules))
            {
                if(preg_match('/'.$rules['regexp'].'/', $_POST[$field]) === 0)
                {
                    static::$errors[] = array($field => _('Invalid format'));
                }
            }
            // if set and list
            if(array_key_exists('list', $rules))
            {
                if(!in_array($_POST[$field], $rules['list']))
                {
                    static::$errors[] = array($field => _('Value not allowed'));
                }
            }
            // if set and field match
            if(array_key_exists('field-match', $rules))
            {
                if($_POST[$field] !== $_POST[$rules['field-match']])
                {
                    static::$errors[] = array($field => _('This value should match with ').$rules['field-match']);
                }
            }
            static::$valid[$field] = $_POST[$field];
        }
        return (0 === count(static::$errors));
    }
    
    /**
     * get all rules, per field or an specific one
     * @param type $field
     * @param type $type
     * @return type 
     */
    public static function getRules($field=false, $type=false)
    {
        if($field == false) //all
            return static::$rules;
        if($type == false)    
            return static::$rules[$field];
            
        return static::$rules[$field][$type];
    }
    
    public static function getErrors()
    {
        return static::$errors;
    }
    
    /**
     *
     * @param type $field
     * @return type 
     */
    public static function getValid($field=false)
    {
        if(isset(static::$valid[$field]))
            return static::$valid[$field];
        
        return static::$valid;
    }
    
}