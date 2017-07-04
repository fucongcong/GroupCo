<?php

namespace src\Service\{{name}}\Service\Rely;

use Group\Sync\Service;

abstract class {{name}}BaseService extends Service
{
    public function get{{name}}Dao()
    {
        return $this->createDao("{{name}}:{{name}}");
    }
}