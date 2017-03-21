<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

// require short functions
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

/**
 * Loader Class
 *
 * - Load class file automatically by class name
 * - Or requrire source file manually by specified path
 *
 * @package     PhalApi\Loader
 * @link        http://docs.phalconphp.com/en/latest/reference/loader.html
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-01-28
 */ 

class PhalApi_Loader {

    /**
     * @var     array   $dirs       the folders to be required
     */
    protected $dirs = array();
    
    /**
     * @var     string  $basePath   the root path
     */
    protected $basePath = '';

    public function __construct($basePath, $dirs = array()) {
        $this->setBasePath($basePath);

        if (!empty($dirs)) {
            $this->addDirs($dirs);
        }

        spl_autoload_register(array($this, 'load'));
    }
    
    /**
     * Add the folders to by required
     * 
     * @param   string  $dirs   the absolute folder paths to by required
     * @return  NULL
     */
    public function addDirs($dirs) {
        if(!is_array($dirs)) {
            $dirs = array($dirs);
        }

        $this->dirs = array_merge($this->dirs, $dirs);
    }

    /**
     * Set the root path
     * 
     * @param   string  $path   root path
     * @return  NULL
     */
    public function setBasePath($path) {
        $this->basePath = $path;
    }
    
    /**
     * require specified file manually
     * 
     * @param   string  $filePath   relative file path, or absolute file path
     */
    public function loadFile($filePath) {
        require_once (substr($filePath, 0, 1) != '/' && substr($filePath, 1, 1) != ':')
            ? $this->basePath . DIRECTORY_SEPARATOR . $filePath : $filePath;
    }
    
    /** ------------------ internal implementation ------------------ **/

    /**
     * Autoload
     * 
     * Here, the reason why we won't throw exception when class is not found,
     * because we hope developers or other library have chance to load it later.
     *
     * @param   string  $className      the class name to be required
     */ 
    public function load($className) {
        if (class_exists($className, FALSE) || interface_exists($className, FALSE)) {
            return;
        }

        if ($this->loadClass(PHALAPI_ROOT, $className)) {
            return;
        }

        foreach ($this->dirs as $dir) {
            if ($this->loadClass($this->basePath . DIRECTORY_SEPARATOR . $dir, $className)) {
                return;
            }
        }
    }

    protected function loadClass($path, $className) {
        $toRequireFile = $path . DIRECTORY_SEPARATOR 
            . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        
        if (file_exists($toRequireFile)) {
            require_once $toRequireFile;
            return TRUE;
        }

        return FALSE;
    }
}
