<?php

namespace Group\Console\Command;

use Group\Console\Command as Command;
use Filesystem;
use Config;

class SqlGenerateCommand extends Command
{
    public function init()
    {
        date_default_timezone_set(Config::get('app::timezone'));
        $dir = __ROOT__."app";
        $filesystem = new Filesystem();
        $filesystem->mkdir($dir."/sql");

        $name = "Sql".date('YmdHis', time());
        $data = $this->getFile("sql.tpl", $name);
        file_put_contents ($dir."/sql/".$name.".php", $data);

        $this->outPut('脚本'.$name.".php创建成功");
    }

    private function getFile($tpl, $name)
    {
        $data = file_get_contents(__DIR__."/../tpl/{$tpl}");

        return $this->getData($data, $name);
    }

    private function getData($data, $name)
    {
        return str_replace("{{name}}", $name, $data);
    }
}
