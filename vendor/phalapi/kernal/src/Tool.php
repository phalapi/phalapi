<?php
namespace PhalApi;

use PhalApi\Exception\BadRequestException;

/**
 * PhalApi_Tool 工具集合类
 * 只提供通用的工具类操作，目前提供的有：
 * - IP地址获取
 * - 随机字符串生成
 * @package     PhalApi\Tool
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-12
 */

class Tool {

    /**
     * IP地址获取
     * @return string 如：192.168.1.1 失败的情况下，返回空
     */
    public static function getClientIp() {
        $unknown = 'unknown';

        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)) {
            $ip = getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        return $ip;
    }

    /**
     * 随机字符串生成
     *
     * @param int    $len 需要随机的长度，不要太长
     * @param string $chars 随机生成字符串的范围
     *
     * @return string
     */
    public static function createRandStr($len, $chars = null) {
        if (!$chars) {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        
        return substr(str_shuffle(str_repeat($chars, rand(5, 8))), 0, $len);
    }

    /**
     * 获取数组value值不存在时返回默认值
     * 不建议在大循环中使用会有效率问题
     *
     * @param array      $arr     数组实例
     * @param string|int $key     数据key值
     * @param string     $default 默认值
     *
     * @return string
     */
    public static function arrIndex($arr, $key, $default = '') {

        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    /**
     * 根据路径创建目录或文件
     *
     * @param string $path 需要创建目录路径
     *
     * @throws PhalApi_Exception_BadRequest
     */
    public static function createDir($path) {

        $dir  = explode('/', $path);
        $path = '';
        foreach ($dir as $element) {
            $path .= $element . '/';
            if (!is_dir($path) && !mkdir($path)) {
                throw new BadRequestException(
                    T('create file path Error: {filePath}', array('filepath' => $path))
                );
            }
        }
    }

    /**
     * 删除目录以及子目录等所有文件
     *
     * - 请注意不要删除重要目录！
     *
     * @param string $path 需要删除目录路径
     */
    public static function deleteDir($path) {

        $dir = opendir($path);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $path . '/' . $file;
                if (is_dir($full)) {
                    self::deleteDir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($path);
    }

    /**
     * 数组转XML格式
     * 
     * @param array $arr 数组
     * @param string $root 根节点名称
     * @param int $num 回调次数
     * 
     * @return string xml
     */
    public static function arrayToXml($arr, $root='xml', $num=0){
        $xml = '';
        if(!$num){
            $num += 1;
            $xml .= '<?xml version="1.0" encoding="utf-8"?>';
        }
        $xml .= "<$root>";
        foreach ($arr as $key=>$val){
            if(is_array($val) || is_object($val)){
                $xml.=self::arrayToXml($val,"$key",$num);
            } else {
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml .="</$root>";
        return $xml;
    }
    /**
     * XML格式转数组
     *
     * @param  string $xml
     *
     * @return mixed|array
     */
    public static function xmlToArray($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $arr = json_decode(json_encode($xmlstring),true);
        return $arr;
    }
    /**
     * 去除字符串空格和回车
     *
     * @param  string $str 待处理字符串
     *
     * @return string
     */
    public static function trimSpaceInStr($str)
    {
        $pat = array(" ", "　", "\t", "\n", "\r");
        $string = array("", "", "", "", "", );
        return str_replace($pat, $string, $str);
    }
}
