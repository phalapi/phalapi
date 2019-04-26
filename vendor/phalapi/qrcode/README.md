# 二维码扩展

PhalApi 二维码扩展，基于[PHP QRCode](http://phpqrcode.sourceforge.net)实现。可用于生成二维码图片。  


## 安装和配置
修改项目下的composer.json文件，并添加：  
```
    "phalapi/qrcode":"dev-master"
```
然后执行```composer update```。  

## 注册
在/path/to/phalapi/config/di.php文件中，注册：  
```php
$di->qrcode = function() {
    return new \PhalApi\QrCode\Lite();
};
```

## 使用
第一种使用方式：直接输出二维码图片：
```php
\PhalApi\DI()->qrcode->png('Hello PhalApi!', false, 'L', 4);
```

效果类似如下：  
![](http://7xiz2f.com1.z0.glb.clouddn.com/20171121225722_9b8f48f3986e2026363584dba7c56621)

或者直接浏览器访问：[http://api.phalapi.net/?s=QrCode.Png&data=Hello%20PhalApi!&size=10](http://api.phalapi.net/?s=QrCode.Png&data=Hello%20PhalApi!&size=10)  

第二种使用方式：将二维码图片保存到文件。
```
\PhalApi\DI()->qrcode->png('Hello PhalApi!', '/path/to/your_file.png', 'L', 4);
```

## 代码示例
可参考[PhalApi的示例源代码](https://github.com/phalapi/phalapi/blob/master-2x/src/app/Api/Examples/QrCode.php)。