<?php

namespace Group\Handlers;

use Group\App\App;
use Group\Events\ExceptionEvent;
use Group\Events\KernalEvent;

class ExceptionsHandler
{
    /**
     * App
     *
     * @var App
     */
    protected $container;

    private $levels = array(
        E_WARNING => 'Warning',
        E_NOTICE => 'Notice',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'User Deprecated',
        E_ERROR => 'Error',
        E_CORE_ERROR => 'Core Error',
        E_COMPILE_ERROR => 'Compile Error',
        E_PARSE => 'Parse',
    );

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  App  $app
     * @return void
     */
    public function bootstrap()
    {
        error_reporting(-1);

        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);

        register_shutdown_function([$this, 'handleShutdown']);

        ini_set('display_errors', 'Off');
    }

    /**
     * Convert a PHP error to an ErrorException.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     * @return void
     *
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            $error = [
                'message' => $message,
                'file'    => $file,
                'line'    => $line,
                'type'    => $level,
            ];

            switch ($level) {
                case E_USER_ERROR:
                    $this->record($error);
                    if ($this->container->runningInConsole()) {
                        $this->renderForConsole($e);
                    } else {
                        $this->renderHttpResponse($e);
                    }
                    break;
                default:
                    $this->record($error, 'warning');
                    break;
            }
            return true;
        }

        return false;
    }

    public function handleException($e)
    {
        $error = [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
            'type'    => $e->getCode(),
        ];

        $this->record($error);
        if ($this->container->runningInConsole()) {
            $this->renderForConsole($error);
        } else {
            $this->renderHttpResponse($error);
        }
    }

    protected function renderForConsole($e)
    {
        $this->renderHttpResponse($e);
    }

    /**
     * Render an exception as an HTTP response and send it.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function renderHttpResponse($e)
    {
        //dev下面需要render信息
        if ($this->container->getEnvironment() == 'prod') {
            $controller = new \Controller($this->app);
            $e = $controller->twigInit()->render(\Config::get('view::error_page'));
        }else {
            if (!is_array($e)) {
                $trace        = debug_backtrace();
                $error['message'] = $e;
                $error['file']    = $trace[0]['file'];
                $error['line']    = $trace[0]['line'];
                ob_start();
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                $e['trace'] = ob_get_clean();
                $e = $error;
            }
        }

        $this->container->singleton('eventDispatcher')->dispatch(KernalEvent::EXCEPTION, new ExceptionEvent($e, $this->container));
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @return void
     */
    public function handleShutdown()
    {
        if ($e = error_get_last()) {
            if ($this->isFatal($e['type'])) {
                $this->record($e);
                $e['trace'] = '';
                if ($this->container->runningInConsole()) {
                    $this->renderForConsole($e);
                } else {
                    $this->renderHttpResponse($e);
                }
            }
                
        }
    }

    protected function record($e, $type = 'error')
    {   
        if (!isset($this->levels[$e['type']])) {
            $level = 'Task Exception';
        } else {
            $level = $this->levels[$e['type']];
        }

        //要异步 否则报错
        //\Log::$type('[' . $level . '] ' . $e['message'] . '[' . $e['file'] . ' : ' . $e['line'] . ']', []);
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param  int  $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

}
