<?php
namespace core;
/**
 * Base controller
 *
 * @author rodrigo
 */
abstract class Controller 
{

    /**
     * variables available on views
     * @var array 
     */
    protected $params = array();
    /**
     * content of views (partials or full page)
     * @var array
     */
    protected $views = array();
    /**
     * concept of layout is a full html template with view variables inside
     * params array is not available, use vars parameter of the function setLayout()
     * @var string 
     */
    protected $layout = null;
  
    public function __construct() 
    {
        if(DEBUG_SYSTEM)
            echo '<br />'.__METHOD__.': '.microtime(true);
    }
    
    /**
     *
     * @param type $params 
     */
    public function preDispatch()
    {
        if(DEBUG_SYSTEM)
            echo '<br />'.__METHOD__.': '.microtime(true);
    }
    
    public function postDispatch()
    {
        if(DEBUG_SYSTEM)
            echo '<br />'.__METHOD__.': '.microtime(true);
    }
    
    /**
     *
     * @param type $view view file name
     * @param type $key view key, should match with layout key if you want to embed
     * @param type $vars parameters for this view scope only
     */
    public function setView($view, $key, $vars=false)
    {
        if(DEBUG_SYSTEM)
            echo '<br />'.__METHOD__.': '.microtime(true);

        if(is_array($vars))
            $vars = array_merge($this->params, $vars);
        
        $this->views[$key] = Core::view($view, $vars);
    }
    
    /**
     *
     * @param type $name
     * @param type $vars
     * @return type 
     */
    public function setLayout($name, $vars=false)
    {
        if(DEBUG_SYSTEM)
            echo '<br />'.__METHOD__.': '.microtime(true);
        
        if(is_array($vars))
            $vars = array_merge($this->views, $vars);
        else
            $vars = $this->views;
        $this->layout = Core::layout($name, $vars);
    }
    
    /**
     * ajax support
     * @param type $return
     * @return type 
     */
    public function output($return=false)
    {
        if(DEBUG_SYSTEM)
            echo '<br />'.__METHOD__.': '.microtime(true);
        
        // ajax requests send views contents (use arrays!)
        if(Core::isAjaxRequest())
            return $this->views;
        
        if($return === false)
        {
            echo ($this->layout) ? $this->layout : implode(null, $this->views);
        }
        return ($this->layout) ? $this->layout : implode(null, $this->views);
    }
    
    /**
     *
     * @param type $uri 
     */
    protected function redirect($uri)
    {
        if(Core::isAjaxRequest())
        {
            $this->views['redirect'] = $uri;
            return true;
        }
        // not ajax, end of the execution
        header('Status: 200');
        header('Location: '.$uri);
        session_write_close();
        exit(0);
    }
    
}