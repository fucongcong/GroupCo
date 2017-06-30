<?php

namespace Group\Common;

use Group\Exceptions\NotFoundException;

Class ClassMap
{
    protected $dir;

    public function __construct($dir = ['src/Services'])
    {
        $this ->dir = $dir;
    }

    public  function doSearch()
    {
        $data = array();

        foreach ($this->dir as $key => $value) {
            $data = $this->searchClass($value, $data);
        }

        return $data;
    }

    private  function searchClass($fileDir, $data=[])
    {
        if (is_dir(__ROOT__.$fileDir)) {
            $dir = opendir(__ROOT__.$fileDir);

            while (($file = readdir($dir)) !== false)
            {
                $file = explode(".", $file);
                $fileName = $file[0];

                if ($fileName && isset($file[1]) && $file[1] =="php" && substr($fileName, -4) == "Impl") {

                    $model = explode("/", $fileDir);
                    $model = $model[2];

                    $class = $fileDir."/".$fileName;
                    $class = str_replace("/", "\\", $class);

                    if (!class_exists($class)) {
                        throw new NotFoundException("Class ".$class." not found !");
                    }

                    $name = substr($fileName, 0, -4);
                    $data[] = [$class, $model."_".$name];

                }else if ($fileName) {
                    $data = $this->searchClass($fileDir."/".$fileName, $data);
                }
            }
            closedir($dir);
        }
        return $data;
    }
}
