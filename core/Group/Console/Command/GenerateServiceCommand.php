<?php

namespace Group\Console\Command;

use Group\Console\Command as Command;
use Filesystem;

class GenerateServiceCommand extends Command
{
    public function init()
    {
        $input = $this->getArgv();

        if (!isset($input[0])) {
            $this->error("名称不能为空！");
        }

        $name = $input[0];
        if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $this->error("名称只能为英文！");
        }

        $serviceName = ucfirst($name);
        $this->outPut('开始初始化'.$serviceName.'Service...');

        $dir = __ROOT__."src/Async";

        $this->outPut('正在生成目录...');

        if (is_dir($dir."/".$serviceName)) {
            $this->error('目录已存在...初始化失败');
        }

        $filesystem = new Filesystem();
        $filesystem->mkdir($dir."/".$serviceName."");
        $filesystem->mkdir($dir."/".$serviceName."/Dao/Impl");
        $filesystem->mkdir($dir."/".$serviceName."/Service/Impl");
        $filesystem->mkdir($dir."/".$serviceName."/Service/Rely");

        $this->outPut('开始创建模板...');
        $data = $this->getFile("Service.tpl", $serviceName);
        file_put_contents ($dir."/".$serviceName."/Service/".$serviceName."Service.php", $data);

        $data = $this->getFile("ServiceImpl.tpl", $serviceName);
        file_put_contents ($dir."/".$serviceName."/Service/Impl/".$serviceName."ServiceImpl.php", $data);

        $data = $this->getFile("BaseService.tpl", $serviceName);
        file_put_contents ($dir."/".$serviceName."/Service/Rely/".$serviceName."BaseService.php", $data);

        $data = $this->getFile("Dao.tpl", $serviceName);
        file_put_contents ($dir."/".$serviceName."/Dao/".$serviceName."Dao.php", $data);

        $data = $this->getFile("DaoImpl.tpl", $serviceName);
        file_put_contents ($dir."/".$serviceName."/Dao/Impl/".$serviceName."DaoImpl.php", $data);

        $this->outPut('初始化'.$serviceName.'Service完成');
    }

    private function getFile($tpl, $serviceName)
    {
        $data = file_get_contents(__DIR__."/../tpl/{$tpl}");

        return $this->getData($data, $serviceName);
    }

    private function getData($data, $serviceName)
    {
        return str_replace("{{name}}", $serviceName, $data);
    }
}
