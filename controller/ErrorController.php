<?php
namespace controller;
use core\Core as Core;
use core\Session as Session;

/**
 * Error 404 controller with ajax support
 *
 * @author hrodicus@gmail.com
 */
class ErrorController extends Controller
{
    
    /**
     * not found
     */
    public function notFound404()
    {
        $vars = array('request' => print_r(Session::getFlash('request'), true));
        
        if(Core::isAjaxRequest())
        {
            echo json_encode(array('error' => _('La página solicitada no existe')."\n".$vars['request']));
            exit();
        }
        
        $this->setView('error/404', false, $vars);
    }
    
}