<?php

namespace Group\Debug\Collector;

class VarCollector extends \DebugBar\DataCollector\ConfigCollector
{
    protected $name;

    protected $data;

    /**
     * @param array  $data
     * @param string $name
     */
    public function __construct(array $data = array(), $name = 'view')
    {
        $this->name = $name;
        $this->data[] = $data;
    }

    /**
     * Sets the data
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data[] = $data;
    }

    /**
     * @return array
     */
    public function collect()
    {
        $data = array();
        if ($this->data) {
            $this->data = $this->data[0];
        }
        foreach ($this->data as $k => $v) {
            if (!is_string($v)) {
                $v = $this->getDataFormatter()->formatVar($v);
            }
            $data[$k] = $v;
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        $name = $this->getName();
        return array(
            "$name" => array(
                "icon" => "gear",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "$name",
                "default" => "{}"
            )
        );
    }
}
