<?php

namespace Group\Common;

class StringToolkit
{
    /**
     * 获取字符串的长度
     *
     * 计算时, 汉字或全角字符占2个长度, 英文字符占1个长度
     * @param string  $str
     * @param boolean $filter 是否过滤html标签
     * @return int 字符串的长度
     */
    public static function getLength($str, $filter = false) {
        if ($filter) {
            $str = html_entity_decode($str, ENT_QUOTES);
            $str = strip_tags($str);
        }
        return (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
    }

    //getShort会清理掉所有的样式
    public static function getShort($str, $length = 40, $ext = '&hellip;') {
        $str = strip_tags($str);
        $str = htmlspecialchars($str);
        $strlenth = 0;
        $output = '';
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
        foreach ($match[0] as $v) {
            preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs);
            if (!empty($matchs[0])) {
                $strlenth += 1;
            } elseif (is_numeric($v)) {
                $strlenth += 0.545;
            } else {
                $strlenth += 0.475;
            }

            if ($strlenth > $length) {
                $output .= $ext;
                break;
            }

            $output .= $v;
        }
        $output = htmlspecialchars_decode($output);
        return $output;
    }

    //按照字符数进行限制,但是保留所有的tag
    public static function truncateHtml( $text, $length = 40, $ellipsis = '&hellip;' ) {

        if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        $totalLength = mb_strlen(strip_tags($ellipsis));
        $openTags = array();
        $truncate = '';

        preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
        foreach ($tags as $tag) {
            if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                    array_unshift($openTags, $tag[2]);
                } elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                    $pos = array_search($closeTag[1], $openTags);
                    if ($pos !== false) {
                        array_splice($openTags, $pos, 1);
                    }
                }
            }
            $truncate .= $tag[1];

            $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
            if ($contentLength + $totalLength > $length) {
                $left = $length - $totalLength;
                $entitiesLength = 0;
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entitiesLength <= $left) {
                            $left--;
                            $entitiesLength += mb_strlen($entity[0]);
                        } else {
                            break;
                        }
                    }
                }

                $truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
                break;
            } else {
                $truncate .= $tag[3];
                $totalLength += $contentLength;
            }
            if ($totalLength >= $length) {
                break;
            }
        }
        $truncate .= $ellipsis;

        foreach ($openTags as $tag) {
            $truncate .= '</' . $tag . '>';
        }

        return $truncate;
    }

    /**
     * 获取文章的长度
     *
     * 计算时, 汉字或英文或者标点和全角字符都1个长度
     * @param string  $str
     * @note 会过滤掉全部的html标签和文字内的空格
     * @return int 字符串的长度
     */
    public static function getWordCount($str) {
        $count = mb_strlen( str_replace(array(' ','　'), '', strip_tags($str) ), 'UTF-8');
        if($count > 99999){
            $count = '10万+';
        }
        return $count;
    }
}
