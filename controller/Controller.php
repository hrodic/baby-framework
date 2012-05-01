<?php
namespace controller;
use helper\I18n as I18n;
use helper\Account as Account;

/**
 * Base site controller
 *
 * @author hrodicus@gmail.com
 */
class Controller extends \core\Controller
{
 
    public function preDispatch() 
    {
        parent::preDispatch();
        I18n::setup();
    }
    
    public function postDispatch() 
    {
        parent::postDispatch();
    }
}

