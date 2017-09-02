<?php
/**
 * PhalApi_Helper_TestRunner - 快速接口执行 - 辅助类
 * 
 * - 使用示例：
```
 * public function testWhatever() {
 *		//Step 1. 构建请求URL
 *		$url = 'service=Default.Index&username=dogstar';
 *		
 *		//Step 2. 执行请求	
 *		$rs = PhalApi_Helper_TestRunner::go($url);
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
 * @author      dogstar <chanzonghuang@gmail.com> 2015-05-30
 */

class PhalApi_Helper_TestRunner {

    /**
     * @param string $url 请求的链接
     * @param array $param 额外POST的数据
     * @return array 接口的返回结果
     */
    public static function go($url, $params = array()) {
        parse_str($url, $urlParams);
        $params = array_merge($urlParams, $params);

        if (!isset($params['service']) && !isset($params['s'])) {
            throw new PhalApi_Exception(T('miss service in url'));
        }
        DI()->request = new PhalApi_Request($params);

        $apiObj = PhalApi_ApiFactory::generateService(true);
        $action = DI()->request->getServiceAction();

        $rs = $apiObj->$action();

        return $rs;
    }
}

