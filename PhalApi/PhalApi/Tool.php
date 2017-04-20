<?php

/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * Tool Class
 *
 * Only provide common tool, currently support:
 *
 * - get IP address
 * - generate random string
 *
 * @package     PhalApi\Tool
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-12
 */
class PhalApi_Tool {

    /**
     * Get IP address
     *
     * @return  string  such as: 192.168.1.1, return empty string when fail
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
     * Generate random string
     *
     * @param   int     $len    the length or random string
     * @param   string  $chars  random chars source
     * @return  string
     */
    public static function createRandStr($len, $chars = null) {
        if (!$chars) {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        
        return substr(str_shuffle(str_repeat($chars, rand(5, 8))), 0, $len);
    }

    /**
     * Get the value in the array if exists, or return default value
     *
     * @param array      $arr     array
     * @param string|int $key     index in the array
     * @param string     $default default value
     *
     * @return mixed
     */
    public static function arrIndex($arr, $key, $default = '') {

        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    /**
     * Create folder or file with path
     *
     * @param   string  $path   path to be created
     *
     * @throws PhalApi_Exception_BadRequest
     */
    public static function createDir($path) {

        $dir  = explode('/', $path);
        $path = '';
        foreach ($dir as $element) {
            $path .= $element . '/';
            if (!is_dir($path) && !mkdir($path)) {
                throw new PhalApi_Exception_BadRequest(
                    T('create file path Error: {filePath}', array('filepath' => $path))
                );
            }
        }
    }

    /**
     * Delete folder
     *
     * - DO NOT delete any IMPORTANT folders!
     *
     * @param string $path path to be deleted
     */
    public static function deleteDir($path) {

        $dir = opendir($path);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $path . '/' . $file;
                if (is_dir($full)) {
                    PhalApi_Tool::deleteDir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($path);
    }

}
