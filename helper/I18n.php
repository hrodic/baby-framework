<?php
namespace helper;
use core\Core as Core;

class I18n
{
    /**
     * Internationalization
     * requires php-gettext on server and locales
     */
    public static function setup()
    {   
       
        if(isset($_GET['locale']))
        {
            Core::setParam('locale', $_GET['locale']);
            setcookie('locale', base64_encode(Core::getParam('locale')), time()+2592000, '/', null, isset($_SERVER["HTTPS"]), true);
        }
        elseif(isset($_COOKIE['locale']))
        {
            Core::setParam('locale', base64_decode($_COOKIE['locale']));
        }
        if(!Core::getParam('locale'))
        {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) 
            {
                foreach (explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) as $accept) 
                {
                    if (preg_match("!([a-z-]+)(;q=([0-9\\.]+))?!", trim($accept), $found)) 
                    {
                        $langs[] = $found[1];
                        $quality[] = (isset($found[3]) ? (float) $found[3] : 1.0);
                    }
                }
                array_multisort($quality, SORT_NUMERIC, SORT_DESC, $langs);
                foreach ($langs as $lang) 
                {
                   $lang = substr($lang,0,2);
                   if (($locale = self::getLocaleByLang($lang)) !== false)
                   {
                       Core::setParam('locale', $locale);
                       break;
                   }
                }
            }
        }
        if(!Core::getParam('locale'))
        {
            Core::setParam('locale', 'en_GB');
        }
        
        // gettext
        putenv('LC_ALL='.Core::getParam('locale'));
        putenv('LANG='.Core::getParam('locale'));        
        setlocale(LC_MESSAGES, Core::getParam('locale').'.utf8');
        // Specify location of translation tables
        bindtextdomain("messages", APPLICATION_PATH . '/locale');
        bind_textdomain_codeset("messages", 'UTF-8');
        textdomain("messages"); 
        
        // monetary
        setlocale(LC_MONETARY, Core::getParam('locale').'.utf8');
        Core::setParam('localeconv', localeconv());
    }
    
    public static function getLocaleByLang($lang)
    {
        $locales = array(
            'es'    =>  'es_ES',
            'en'    =>  'en_US'
        );
        if(isset($locales[$lang]))
            return $locales[$lang];
        
        return false;
    }
}