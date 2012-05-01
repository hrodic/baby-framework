<?php
namespace controller;
use core\Core as Core;
use model\form as form;
use core\Session as Session;

/**
 * Index controller
 *
 * @author rodrigo
 */
class IndexController extends Controller
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function preDispatch()
    {
        parent::preDispatch();
        if(\helper\Account::isAnonymous() === false){
            $this->redirect('/account');
        }
    }
    
    /**
     * Index page
     */
    public function index()
    {   
        $this->setView('partial/content/index', 'content', array());
        $this->setView('partial/footer', 'footer');
        $this->setLayout('default');
    }
    
    public function signinSubmit()
    {
        if(form\Signin::validate())
        {
            Session::set('username', form\Signin::getValid('username'));
            $this->redirect('/account');
        }
        else
        {
            $errors = form\Signin::getErrors();
            $this->views['formErrors'] = $errors;
        }
    }
    
    /**
     * signup form action
     */
    public function signupSubmit()
    {
        if(form\Signup::validate())
        {
            $r = form\Signup::sendConfirmationMail(); 
            if($r)
            {
                Session::setFlash('signupForm', form\signup::getValid());
                $this->views['load'] = array('domId' => 'view-content', 'url' => '/?c=index&m=signupSuccess');
            }
            else
            {
                $this->views['error'] = _('We are sorry but the signup process failed, cannot send email! Try again later or contact us.');
            }
                
        }
        else
        {
            $errors = form\Signup::getErrors();
            $this->views['formErrors'] = $errors;
        }
    }
    
    /**
     * signup form is OK! welcome message :)
     */
    public function signupSuccess()
    {
        $signupForm = Session::getFlash('signupForm');
        if($signupForm)
        {
            $this->setView('/content/signupSuccess', 'success', array('signupForm' => $signupForm));
        }
        else
        {
            $this->views['error'] = _('You should signup first...');
        }
    }
    
    public function recoverPassword()
    {
        
    }
    
}
