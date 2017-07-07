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

        //set_error_handler([$this, 'handleError']);

        //set_exception_handler([$this, 'handleException']);

        //register_shutdown_function([$this, 'handleShutdown']);

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
    // public function handleError($level, $message, $file = '', $line = 0, $context = [])
    // {
    //     if (error_reporting() & $level) {
    //         $error = [
    //             'message' => $message,
    //             'file'    => $file,
    //             'line'    => $line,
    //             'type'    => $level,
    //         ];

    //         switch ($level) {
    //             case E_USER_ERROR:
    //                 //$this->record($error);
    //                 if ($this->container->runningInConsole()) {
    //                     $this->renderForConsole($e);
    //                 } else {
    //                     $this->renderHttpResponse($e);
    //                 }
    //                 break;
    //             default:
    //                 $this->record($error, 'warning');
    //                 break;
    //         }
    //         return true;
    //     }

    //     return false;
    // }

    public function handleException($e)
    {
        $error = [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
            'type'    => $e->getCode(),
        ];

        //$this->record($error);
        return $this->renderHttpResponse($error);
    }

    // protected function renderForConsole($e)
    // {
    //     $this->renderHttpResponse($e);
    // }

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
            $e = $this->container->singleton('twig')->render(\Config::get('view::error_page'));
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

        return $this->trace($e);

        //$this->container->singleton('eventDispatcher')->dispatch(KernalEvent::EXCEPTION, new ExceptionEvent($e, $this->container));
    }

    protected function trace($error)
    {
        if (!is_array($error)) return $error;

        $error['trace'] = str_replace("#", "<br/>", $error['trace']);
        $str = "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><title></title></head><body><style>html, body {height: 100%;}body {margin: 0;padding: 0;width: 100%; display: table;font-weight: 100;font-family: 'Lato';}
.container {margin-top: 100px; vertical-align: middle;width: 1170px;margin-right: auto;margin-left: auto;}.content {text-align: left;display: inline-block;
}.title {font-size: 16px;}h3{color:#a94442;}p {color:#3c763d;}</style><div class=\"container\"><div class=\"content\" style=\"color:#8a6d3b\">
<h2>啊哦！出错了:</h2> </div> <br><div class=\"content\"><h3>错误文件名:</h3><p>{$error['file']}</p></div><br><div class=\"content\">
<h3>line:{$error['line']}</h3></div><br><div class=\"content\"><h3>错误信息:</h3> <p>{$error['message']}</p></div> <br><div class=\"content\"><h3>Trace:</h3><p>{$error['trace']}</p></div><br><div class=\"content\">
<p style=\"color:#31708f\">power by group framework @author:fucongcong;email:cc@xitongxue.com. </p></div></div></body></html>";

        return $str;
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @return void
     */
    // public function handleShutdown()
    // {
    //     if ($e = error_get_last()) {dump(1);
    //         if ($this->isFatal($e['type'])) {
    //             //$this->record($e);
    //             $e['trace'] = '';
    //             $this->renderHttpResponse($e);
    //             exit();
    //         }
                
    //     }
    // }

    // protected function record($e, $type = 'error')
    // {   
    //     if (!isset($this->levels[$e['type']])) {
    //         $level = 'Task Exception';
    //     } else {
    //         $level = $this->levels[$e['type']];
    //     }
    //     //dump('[' . $level . '] ' . $e['message'] . '[' . $e['file'] . ' : ' . $e['line'] . ']', []);
    //     //要异步 否则报错
    //     //\Log::$type('[' . $level . '] ' . $e['message'] . '[' . $e['file'] . ' : ' . $e['line'] . ']', []);
    // }

    /**
     * Determine if the error type is fatal.
     *
     * @param  int  $type
     * @return bool
     */
    // protected function isFatal($type)
    // {
    //     return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    // }

}
