<?php
namespace PhalApi\Request\Formatter;

use PhalApi\Request\Formatter;
use PhalApi\Request\Formatter\BaseFormatter;
use PhalApi\Exception\BadRequestException;

/**
 * FileFormatter 格式化上传文件
 * @package     PhalApi\Request
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-11-07
 */
class FileFormatter extends BaseFormatter implements Formatter {

    /**
     * 格式化文件类型
     *
     * @param array $rule array('name' => '', 'type' => 'file', 'default' => array(...), 'min' => '', 'max' => '', 'range' => array(...))
     *
     * @throws BadRequestException
     */
    public function parse($value, $rule) {

        $default = isset($rule['default']) ? $rule['default'] : NULL;
        $index = $rule['name'];
        $fileList = array();

        // 未上传 && (有默认值 || 非必须)
        if (!isset($_FILES[$index]) && ($default !== NULL || empty($rule['require']))) {
            return $default;
        }

        if (!isset($_FILES[$index]) || !is_array($_FILES[$index])) {
            $message = isset($rule['message'])
                ? \PhalApi\T($rule['message'])
                : \PhalApi\T('miss upload file: {file}', array('file' => $index));
            throw new BadRequestException($message);
        }

        if (is_array($_FILES[$index]['tmp_name'])) {
            $count = sizeof($_FILES[$index]['tmp_name']);

            for ($i = 0; $i < $count; $i++) {
                $file = array(
                    'name' => $_FILES[$index]['name'][$i],
                    'type' => $_FILES[$index]['type'][$i],
                    'tmp_name' => $_FILES[$index]['tmp_name'][$i],
                    'error' => $_FILES[$index]['error'][$i],
                    'size' => $_FILES[$index]['size'][$i]
                );

                $fileList[] = $this->parseOne($file, $rule);
            }
        } else {
            $file = array(
                'name' => $_FILES[$index]['name'],
                'type' => $_FILES[$index]['type'],
                'tmp_name' => $_FILES[$index]['tmp_name'],
                'error' => $_FILES[$index]['error'],
                'size' => $_FILES[$index]['size']
            );
            // 单个文件直接返回文件信息数组
            return $this->parseOne($file, $rule);
        }

        // 返回文件信息二维数组
        return $fileList;
    }

    protected function parseOne($file, $rule)
    {
        $index = $rule['name'];

        if (!isset($file) || !isset($file['error']) || !is_array($file)) {
            $message = isset($rule['message'])
                ? \PhalApi\T($rule['message'])
                : \PhalApi\T('miss upload file: {file}', array('file' => $index));
            throw new BadRequestException($message);
        }

        if ($file['error'] != UPLOAD_ERR_OK) {
            $message = isset($rule['message'])
                ? \PhalApi\T($rule['message'])
                : \PhalApi\T('fail to upload file with error = {error}', array('error' => $file['error']));
            throw new BadRequestException($message);
        }

        $sizeRule         = $rule;
        $sizeRule['name'] = $sizeRule['name'] . '.size';
        $this->filterByRange($file['size'], $sizeRule);

        if (!empty($rule['range']) && is_array($rule['range'])) {
            $rule['range'] = array_map('strtolower', $rule['range']);
            $this->formatEnumValue(strtolower($file['type']), $rule);
        }

        //对于文件后缀进行验证
        if (!empty($rule['ext'])) {
            $ext = trim(strrchr($file['name'], '.'), '.');
            if (is_string($rule['ext'])) {
                $rule['ext'] = explode(',', $rule['ext']);
            }
            if (!$ext) {
                $message = isset($rule['message'])
                    ? \PhalApi\T($rule['message'])
                    : \PhalApi\T('Not the file type {ext}', array('ext' => json_encode($rule['ext'])));
                throw new BadRequestException($message);
            }
            if (is_array($rule['ext'])) {
                $rule['ext'] = array_map('strtolower', $rule['ext']);
                $rule['ext'] = array_map('trim', $rule['ext']);
                if (!in_array(strtolower($ext), $rule['ext'])) {
                    $message = isset($rule['message'])
                        ? \PhalApi\T($rule['message'])
                        : \PhalApi\T('Not the file type {ext}', array('ext' => json_encode($rule['ext'])));
                    throw new BadRequestException($message);
                }
            }
        }

        return $file;
    }
}
