<?php
namespace PhalApi\CLI;

/**
 * 用于开发命令行应用的扩展类库
 *
 * - Example
```
    $cli = new PhalApi\CLI\Lite();
    $cli->response();
```
 *
 * - Usage
 ```
    ./cli -s Site.Index --username dogstar
``` 
 *
 * @link http://getopt-php.github.io/getOopt-php/example.html
 * @author dogstar <chanzonghuang@gmail.com> 20170205
 */

use GetOpt\GetOpt;
use GetOpt\Option;
use GetOpt\Command;
use GetOpt\Argument;;
use GetOpt\ArgumentException;
use GetOpt\ArgumentException\Missing;

use PhalApi\PhalApi;
use PhalApi\Request;
use PhalApi\ApiFactory;

class Lite {

    public function response() {
        // 解析获取service参数
        $serviceOpt = Option::create('s', 'service', GetOpt::REQUIRED_ARGUMENT)
            ->setDescription('接口服务');
        $helpOpt = Option::create('h', 'help', GetOpt::NO_ARGUMENT)
            ->setDescription('查看帮助信息');

        $settings = array(GetOpt::SETTING_STRICT_OPTIONS => false, GetOpt::SETTING_STRICT_OPERANDS => false);
        $getOpt = new GetOpt(array(
            $serviceOpt,
            $helpOpt
        ), $settings);

        $service = NULL;
        try {
            $getOpt->process();

            $service = $getOpt['service'];
        } catch (\Exception $ex) {
            // 后续统一处理
        }

        // 再转换处理 。。。
        try{
            if ($service === NULL) {
                throw new \Exception("缺少service参数，请使用 -s 或 --service 指定需要调用的API接口。");
            }

            // 获取接口实例
            $rules = array();

            \PhalApi\DI()->request = new Request(array('service' => $service));
            $api = ApiFactory::generateService(false);
            $rules = $api->getApiRules();

            // PhalApi接口参数转换为命令行参数
            $rule2opts = array();
            foreach ($rules as $ruleKey => $ruleItem) {
                // 避免重复参数规则
                if (in_array($ruleItem['name'], array('s', 'service', 'h', 'help'))) {
                    continue;
                }

                $opt = Option::create(null, $ruleItem['name'], !empty($ruleItem['require']) ? GetOpt::REQUIRED_ARGUMENT : GetOpt::OPTIONAL_ARGUMENT);

                $optDesc = array();
                if (!empty($ruleItem['require'])) {
                    $optDesc[] = '必须';
                }

                if (isset($ruleItem['type'])) {
                    $opt->setArgumentName(strtoupper($ruleItem['type']));
                }

                if (isset($ruleItem['default'])) {
                    $default = is_array($ruleItem['default']) ? json_encode($ruleItem['default'], JSON_UNESCAPED_UNICODE) : $ruleItem['default']; 
                    $opt->setDefaultValue($default);

                    $optDesc[] = '默认 ' . $default;
                }

                if (isset($ruleItem['desc'])) {
                    $optDesc[] = strip_tags($ruleItem['desc']);
                }

                if ($optDesc) {
                    $opt->setDescription(implode('；', $optDesc));
                }

                $rule2opts[] = $opt;
            }

            // 添加参数选项，提取命令行参数并重新注册请求
            $getOpt->addOptions($rule2opts);

            $getOpt->process();

            if ($getOpt['help']) {
                echo $getOpt->getHelpText();
                exit(1);
            }

            \PhalApi\DI()->request = new Request($getOpt->getOptions());

            // 转交PhalApi重新响应处理
            $api = new PhalApi();
            $rs = $api->response();
            $rs->output();
        } catch (\Exception $ex) {
            echo $getOpt->getHelpText();
            echo PHP_EOL . $ex->getMessage() . PHP_EOL . PHP_EOL;
            exit(1);
        }
    }
}
