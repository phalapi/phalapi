<?php
namespace PhalApi\Helper;

use PhalApi\Exception;
use PhalApi\Request;
use PhalApi\ApiFactory;

/**
 * TestRunner - 快速接口执行 - 辅助类
 * 
 * - 使用示例：
```
 * public function testWhatever() {
 *		//Step 1. 构建请求URL
 *		$url = 'service=App.Site.Index&username=dogstar';
 *		
 *		//Step 2. 执行请求	
 *		$rs = TestRunner::go($url);
 *		
 *		//Step 3. 验证
 *		$this->assertNotEmpty($rs);
 *		$this->assertArrayHasKey('code', $rs);
 *		$this->assertArrayHasKey('msg', $rs);
 * }
```
 *     
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 20170703
 */

class TestRunner {

    /**
     * @param string $url 请求的链接
     * @param array $param 额外POST的数据
     * @return array 接口的返回结果
     */
    public static function go($url, $params = array()) {
        parse_str($url, $urlParams);
        $params = array_merge($urlParams, $params);

        if (!isset($params['service']) && !isset($params['s'])) {
            throw new Exception(\PhalApi\T('miss service in url'));
        }
        \PhalApi\DI()->request = new Request($params);

        $apiObj = ApiFactory::generateService(true);
        $action = \PhalApi\DI()->request->getServiceAction();

        $rs = $apiObj->$action();

        return $rs;
    }
}

