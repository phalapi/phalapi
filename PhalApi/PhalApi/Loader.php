<?php
/**
 * PhalApi_Loader 自动加载器
 *
 * - 按类名映射文件路径自动加载类文件
 * - 可以自定义加载指定文件
 *
 * @link: http://docs.phalconphp.com/en/latest/reference/loader.html，实现统一的类加载
 * @author dogstar 2014-01-28
 */ 

//加载快速方法
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

class PhalApi_Loader
{
	protected $dirs = array();
	
    protected $basePath = '';

    public function __construct($basePath, $dirs = array())
    {
        $this->setBasePath($basePath);

        if (!empty($dirs)) {
            $this->addDirs($dirs);
        }

    	spl_autoload_register(array($this, 'load'));
    }
    
    public function addDirs($dirs)
    {
        if(!is_array($dirs)) {
            $dirs = array($dirs);
        }

        $this->dirs = array_merge($this->dirs, $dirs);
    }

    public function setBasePath($path)
    {
    	$this->basePath = $path;
    }
    
    public function loadFile($filePath)
    {
        require_once (substr($filePath, 0, 1) != '/' && substr($filePath, 1, 1) != ':')
            ? $this->basePath . DIRECTORY_SEPARATOR . $filePath : $filePath;
    }
    
    /** ------------------ 内部实现 ------------------ **/

    /**
     * 自动加载
     *
     * @param string $className 等待加载的类名
     */ 
    public function load($className)
    {
        if (class_exists($className, false) || interface_exists($className, false)) {
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

    protected function loadClass($path, $className)
    {
        $toRequireFile = $path . DIRECTORY_SEPARATOR 
            . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        
        if (file_exists($toRequireFile)) {
            require_once $toRequireFile;
            return true;
        }

        return false;
    }
}
