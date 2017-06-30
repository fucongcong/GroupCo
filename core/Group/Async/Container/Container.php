<?php

namespace Group\Async\Container;

use ReflectionClass;
use App;
use Group\Exceptions\NotFoundException;

class Container
{   
	private static $instance;

    protected $timezone;

    protected $environment;

    protected $appPath;

    protected $locale;

    protected $debug = false;

    public function __construct()
    {
        $this->setTimezone();

        $this->setEnvironment();

        $this->setLocale();

        $this->needDebug();
    }

	/**
	 * build a moudle class
	 *
	 * @param  class
	 * @return ReflectionClass class
	 */
	public function buildMoudle($class)
	{
		if (!class_exists($class)) {
			throw new NotFoundException("Class ".$class." not found !");
		}

		$reflector = new ReflectionClass($class);

		return $reflector;
	}

    /**
     * do the moudle class action
     *
     * @param  class
     * @param  action
     * @param  array parameters
     * @return string
     */
	public function doAction($class, $action, array $parameters, \Request $request)
	{
		$reflector = $this->buildMoudle($class);

		if (!$reflector->hasMethod($action)) {
			throw new NotFoundException("Class ".$class." exist ,But the Action ".$action." not found");
		}

		$instanc = $reflector->newInstanceArgs(array(App::getInstance(), $this));
		$method = $reflector->getmethod($action);
        $args = [];
        foreach ($method->getParameters() as $arg) {
            $paramName = $arg ->getName();
            if (isset($parameters[$paramName])) $args[$paramName] = $parameters[$paramName];
            if (!empty($arg->getClass()) && $arg->getClass()->getName() == 'Group\Http\Request') $args[$paramName] = $request;
        }

		return $method->invokeArgs($instanc, $args);
	}

    /**
     * return single class
     *
     * @return Group\Container Container
     */
	public static function getInstance()
    {
		if (!(self::$instance instanceof self)){
			self::$instance = new self;
		}

		return self::$instance;
	}

    /**
     * 设置时区
     *
     */
    public function setTimezone()
    {
        $this->timezone = \Config::get('app::timezone');
        date_default_timezone_set($this->getTimezone());
    }


    /**
     * 获取当前时区
     *
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * 获取当前环境
     *
     *@return string prod｜dev
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * 设置环境
     *
     */
    public function setEnvironment()
    {
        $this->environment = \Config::get('app::environment');
    }

    /**
     * 设置系统根目录
     *
     */
    public function setAppPath($path)
    {
        $this->appPath = $path;
    }

    /**
     * 获取系统根目录
     *
     *@return string
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * 设置地区
     *
     */
    public function setLocale()
    {
        $this->locale = \Config::get('app::locale');
    }

    /**
     * 获取设置的地区
     *
     *@return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function runningInConsole()
    {
        return php_sapi_name() == 'cli';
    }

    private function needDebug()
    {
        if (\Config::get('app::environment') == "dev" && \Config::get('app::debug')) {
            $this->debug = true;
        }
    }

    public function isDebug()
    {
        return $this->debug;
    }
}
