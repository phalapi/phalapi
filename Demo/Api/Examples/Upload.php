<?php
/**
 * 文件上传示例
 * 
 * 测试页面： http://localhost/Public/demo/upload.html
 *
 * @author dogstar 20170611
 */

class Api_Examples_Upload extends PhalApi_Api {

    public function getRules() {
        return array(
            'go' => array(
                'file' => array(
                    'name' => 'file',        // 客户端上传的文件字段
                    'type' => 'file', 
                    'require' => true, 
                    'max' => 2097152,        // 最大允许上传2M = 2 * 1024 * 1024, 
                    'range' => array('image/jpeg', 'image/png'),  // 允许的文件格式
                    'ext' => 'jpeg,jpg,png', // 允许的文件扩展名 
                    'desc' => '待上传的图片文件',
                ),
            ),
        );
    }   

    /**
     * 图片文件上传
     * @desc 只能上传单个图片文件
     * @return int code 操作状态码，0成功，1失败
     * @return url string 成功上传时返回的图片URL
     */
    public function go() {
        $rs = array('code' => 0, 'url' => '');

        $tmpName = $this->file['tmp_name'];

        $name = md5($this->file['name']);
        $ext = strrchr($this->file['name'], '.');
        $uploadFolder = sprintf('%s/Public/upload/', API_ROOT);
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0777);
        }

        $imgPath = $uploadFolder .  $name . $ext;
        if (move_uploaded_file($tmpName, $imgPath)) {
            $rs['code'] = 1;
            $rs['url'] = sprintf('http://%s/upload/%s%s', $_SERVER['SERVER_NAME'], $name, $ext);
        }

        return $rs;
    }
}
