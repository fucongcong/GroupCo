<?php

namespace Group\Cache;

use Composer\Autoload\ClassLoader;

class BootstrapClass
{   
    private static $instance;

    private $loader;

    protected $cacheDir = "runtime/cache/bootstrap.class.cache";

    protected $classes;

    public function __construct($loader, $cacheDir = null)
    {
        $this->loader = $loader;
        if ($cacheDir) $this->cacheDir = $cacheDir;
    }

    public function setClass($class) 
    {
        $file = $this->loader->findFile($class);
        $this->classes[$class] = $file;
    }

    public function rmClass($class) 
    {
        $file = $this->loader->findFile($class);
        unset($this->classes[$class]);
    }

    public function bootstrap()
    {      
        $data = "<?php";
        foreach ($this->classes as $class => $file) {
            $data .= substr(file_get_contents($file), 5); 
        }
        
        $parts = explode('/', $this->cacheDir);
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= "$part/")) {
                 mkdir($dir);
            }
        }
        
        file_put_contents("$dir/$file", $data);
    }

    /**
     * return single class
     *
     * @return Group\Cache BootstrapClass
     */
    public static function getInstance(){

        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}
