<?php

namespace src\Async\{{name}}\Service\Rely;

use Group\Async\Service;

abstract class {{name}}BaseService extends Service
{
    public function get{{name}}Dao()
    {
        return $this->createDao("{{name}}:{{name}}");
    }
}