<?php

namespace Group\Events;

final class ExceptionEvent extends \Event
{   
    protected $error;

    protected $container;

    public function __construct($error, $container)
    {
        $this->error = $error;
        $this->container = $container;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getTrace()
    {
        return $this->trace($this->error);
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
}
