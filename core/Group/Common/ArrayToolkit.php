<?php

namespace Group\Common;

class ArrayToolkit
{
    /**
     * 返回数组中key对应的值
     *
     * @param  array
     * @param  columnName
     * @return array
     */
	public static function column(array $array, $columnName)
	{
		if (empty($array)) {
			return array();
		}

		$column = array();
		foreach ($array as $item) {
            if (isset($item[$columnName])) {
                $column[] = $item[$columnName];
            }
		}
    	return $column;
	}

    /**
     * 过滤数组中的key
     *
     * @param  array
     * @param  keys
     * @return array
     */
	public static function parts(array $array, array $keys)
	{
		foreach (array_keys($array) as $key) {
			if (!in_array($key, $keys)) {
				unset($array[$key]);
			}
		}
		return $array;
	}

    /**
     * 数组中的key是否存在
     *
     * @param  array
     * @param  keys
     * @return boolean
     */
	public static function requireds(array $array, array $keys)
	{
		foreach ($keys as $key) {
			if (!array_key_exists($key, $array)) {
				return false;
			}
		}
		return true;
	}

    /**
     * 数组间的差异
     *
     * @param  array
     * @param  array
     * @return array
     */
	public static function changes(array $before, array $after)
	{
		$changes = array('before' => array(), 'after' => array());
		foreach ($after as $key => $value) {
			if (!isset($before[$key])) {
				continue;
			}
			if ($value != $before[$key]) {
				$changes['before'][$key] = $before[$key];
				$changes['after'][$key] = $value;
			}
		}
		return $changes;
	}

    /**
     * 根据指定key进行分组
     *
     * @param  array
     * @param  key
     * @return array
     */
    public static function group(array $array, $key)
    {
        $grouped = array();
        foreach ($array as $item) {
            if (empty($grouped[$item[$key]])) {
                $grouped[$item[$key]] = array();
            }
            $grouped[$item[$key]][] = $item;
        }

        return $grouped;
    }

    /**
     * 把指定key作为数组键名返回
     *
     * @param  array
     * @param  key
     * @return array
     */
    public static function index (array $array, $name)
    {
        $indexedArray = array();
        if (empty($array)) {
            return $indexedArray;
        }

        foreach ($array as $item) {
            if (isset($item[$name])) {
                $indexedArray[$item[$name]] = $item;
                continue;
            }
        }
        return $indexedArray;
    }

    /**
     * 过滤数组中value得值
     *
     * @param  array
     * @param  specialValues
     * @return array
     */
    public static function filter(array $array, array $specialValues)
    {
    	$filtered = array();
    	foreach ($specialValues as $key => $value) {
    		if (!array_key_exists($key, $array)) {
    			continue;
    		}

			if (is_array($value)) {
				$filtered[$key] = (array) $array[$key];
			} elseif (is_int($value)) {
				$filtered[$key] = (int) $array[$key];
			} elseif (is_float($value)) {
				$filtered[$key] = (float) $array[$key];
			} elseif (is_bool($value)) {
				$filtered[$key] = (bool) $array[$key];
			} else {
				$filtered[$key] = (string) $array[$key];
			}

			if (empty($filtered[$key])) {
				$filtered[$key] = $value;
			}
    	}

    	return $filtered;
    }
}
