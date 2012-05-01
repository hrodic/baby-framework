<?php
namespace helper;
use core\Core as Core;

/**
 * Bundler helper
 *
 * @author miguel
 */
class Bundler
{
	
    public static $files = array(
        'JS' => array(
            '/js/lib/bootstrap.min.js',
            '/js/base.js'
        ),
		'CSS' => array(
            '/css/bootstrap.css',
			'/css/bootstrap-responsive.css',
        )
	);
	
	protected static $aLinks = array(
		'JS' => '<script type="text/javascript" src="{file}"></script>',
		'CSS' => '<link type="text/css" rel="stylesheet" media="screen" href="{file}">'
	);
	
    public static function getLink($sType)
    {                
        if( Core::$config['bundle'] )
        {
        	$file = ($sType =='CSS')? self::bundleCSS() : self::bundleJS();
        	echo str_replace('{file}',$file, self::$aLinks[$sType]).PHP_EOL;
        }
        else
        {
        	foreach(self::$files[$sType] as $file)
    			echo str_replace('{file}',$file,self::$aLinks[$sType]).PHP_EOL;
        }
    }

    /**
     *
     * @return type 
     */
    private function bundleJS()
    {  
        $fileName = 'bundle.js';
        $filePath = APPLICATION_PATH . '/public/js/' . $fileName;
        if(!file_exists($filePath) || isset($_GET['rebuild']))
        {
            $minified = self::minifyFiles( self::$files['JS'], APPLICATION_PATH . '/public' );
            file_put_contents($filePath, $minified, FILE_TEXT);
            chmod($filePath, 0755);
        }
        unset($minified);
        return '/js/'.$fileName;
    }

    private function bundleCSS()
    {
        $fileName = 'bundle.css';
        $filePath = APPLICATION_PATH . '/public/css/' . $fileName;
        if(!file_exists($filePath) || isset($_GET['rebuild']))
        {
            $source = '';
            foreach(self::$files['CSS'] as $file)
            {
                $source.= self::minify( file_get_contents(APPLICATION_PATH . '/public/' . $file) );
            }
            file_put_contents($filePath, $source, FILE_TEXT);
            chmod($filePath, 0755);
        }
        unset($minified);
        return '/css/'.$fileName;
    }    
    
    /**
     *
     * @param type $source 
     */
    private function minify($source)
    {
        include_once APPLICATION_PATH . '/thirdparty/minify/JSMin.php';
        return \JSMin::minify($source);
    }
    
    /**
     * minify several files
     * @param type $source
     * @return type 
     */
    private function minifyFiles($files, $path)
    {
        $source = '';
        foreach($files as $file)
        {
            $source.= self::minify(file_get_contents($path.$file)).';';
        }
        return $source;
    }
}
