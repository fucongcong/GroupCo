<?php

namespace Group\Common;

class SecurityToolkit
{
	/**
     * 转换为安全的纯文本
     *
     * @param string  $text
     * @param boolean $parse_br    是否转换换行符
     * @param int     $quote_style ENT_NOQUOTES:(默认)不过滤单引号和双引号 ENT_QUOTES:过滤单引号和双引号 ENT_COMPAT:过滤双引号,而不过滤单引号
     * @return string|null string:被转换的字符串 null:参数错误
     */
    public static function text($text, $parse_br = false, $quote_style = ENT_NOQUOTES) {
        if (is_numeric($text))
            $text = (string)$text;

        if (!is_string($text))
            return null;

        $text = str_replace('\\','\\\\',$text);
        $text = htmlspecialchars($text, $quote_style, 'UTF-8');

        if (!$parse_br) {
            $text = str_replace(array("\r", "\n", "\t"), ' ', $text);
        } else {
            $text = nl2br($text);
        }

        //删除最后的空格
        $text = rtrim($text);

        return $text;
    }

    /**
     * 过滤得到安全的html
     * @param string $text 待过滤的字符串
     * @param array $tags 标签的过滤白名单
     */
    public static function html($text, $tags = null) {
        $text = trim($text);
        //过滤换行符
        $text = preg_replace('/\r?\n/', '', $text);
        //完全过滤注释
        $text = preg_replace('/<!--.*?-->/', '', $text);
        //完全过滤动态代码
        $text = preg_replace('/<\?\?>/', '', $text);
        //完全过滤js
        $text = preg_replace('/<script.*?\/script>/', '', $text);

        $text = str_replace('[', '&#091;', $text);
        $text = str_replace(']', '&#093;', $text);
        $text = str_replace('|', '&#124;', $text);
        //br
        $text = preg_replace('/<br(\s?\/)?>/i', '[br]', $text);
        $text = preg_replace('/(\[br\]\s*){10,}/i', '[br]', $text);
        //过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[a-z]+)(lang|on|action|background|codebase|dynsrc|lowsrc|style|class|width|height|align|hspace|valign)[^><]+/i', $text, $mat)) {
            $text = str_replace($mat[0], $mat[1], $text);
        }
        while (preg_match('/(<[a-z]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_replace($mat[0], $mat[1] . $mat[3], $text);
        }
        if (empty($tags)) {
            $tags = 'table|tbody|td|th|tr|i|b|u|strong|img|p|br|div|span|strong|em|ul|ol|li|dl|dd|dt|a';
            $tags .= '|object|param|embed';
            // 音乐和视频
        }
        //允许的HTML标签
        $text = preg_replace('/<(\/?(?:' . $tags . '))( [^><\[\]]*)?>/i', '[\1\2]', $text);
        //过滤多余html
        $text = preg_replace('/<\/?([a-z]+)[^><]*>/i', '', $text);
        //转换引号
        while (preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2\[\]]+)\2([^\[\]]*\])/i', $text, $mat)) {
            $text = str_replace($mat[0], $mat[1] . '|' . $mat[3] . '|' . $mat[4], $text);
        }
        //过滤错误的单个引号
        while (preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i', $text, $mat)) {
            $text=str_replace($mat[0], str_replace($mat[1], '', $mat[0]), $text);
        }
        //转换其它所有不合法的 < >
        $text = str_replace('<', '&lt;', $text);
        $text = str_replace('>', '&gt;', $text);
        $text = str_replace('"', '&quot;', $text);
        //反转换
        $text = str_replace('[', '<', $text);
        $text = str_replace(']', '>', $text);
        $text = str_replace('|', '"', $text);
        //过滤多余空格
        $text = str_replace('  ', ' ', $text);
        return $text;
    }
}
