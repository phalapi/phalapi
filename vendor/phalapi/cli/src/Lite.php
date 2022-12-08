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

            // 转编号
            $serviceList = $this->getServiceList();
            if ($service !== NULL && isset($serviceList[$service])) {
                $service = $serviceList[$service][0];
            }
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

                    $optDesc[] = '默认 ' . ($default !== '' ? $default : '[空]');
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
                echo $this->getHelpText($getOpt->getHelpText());
                exit(1);
            }

            // 必填参数
            foreach ($rules as $ruleKey => $ruleItem) {
                // 避免重复参数规则
                if (in_array($ruleItem['name'], array('s', 'service', 'h', 'help'))) {
                    continue;
                }

                if (!empty($ruleItem['require']) && !isset($ruleItem['default'])) {
                    $_n = $ruleItem['name'];
                    $_d = isset($ruleItem['desc']) ? $ruleItem['desc'] : $_n;
                    $val = $getOpt[$_n];
                    if ($val === NULL) {
                        throw new \Exception("缺少{$_n}参数，请使用 --{$_n} 指定：{$_d}");
                    }
                }
            }


            $options = $getOpt->getOptions();
            // 同步转编号后的service
            $options['s'] = $options['service'] = $service;

            $options = $this->afterGetOptions($options);

            // 构建全部命令行参数
            \PhalApi\DI()->request = new Request($options);

            // 转交PhalApi重新响应处理
            $api = new PhalApi();
            $rs = $api->response();
            $arr = $rs->getResult();

            echo PHP_EOL . $this->colorfulString('Service: ' . $service, 'NOTE');
            echo PHP_EOL . $this->colorfulString(json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 'SUCCESS') . PHP_EOL;
        } catch (\Exception $ex) {
            echo $this->getHelpText($getOpt->getHelpText());
            echo PHP_EOL . $this->colorfulString('Service: ' . $service, 'NOTE');
            if ($service === NULL) {
                echo PHP_EOL . $this->getServiceListHelpText();
            }
            echo PHP_EOL . $this->colorfulString($ex->getMessage(), 'FAILURE') . PHP_EOL . PHP_EOL;
            exit(1);
        }
    }

    // 完成命令行参数获取后的操作，方便追加公共参数
    protected function afterGetOptions($options) {
        return $options;
    }

    // 提供接口列表，service -> 接口功能说明
    protected function getServiceList() {
        return array(
            // 1 => ['App.Hello.World', '演示接口'],
        );
    }

    // 提示输出
    protected function getServiceListHelpText() {
        $list = $this->getServiceList();

        $topLen = 20;
        foreach ($list as $pos => $it) {
            $topLen = max(strlen($it[0]), $topLen);
        }
        $topLen += 2;

        $text = '';
        foreach ($list as $pos => $it) {
            $text .= $this->colorfulString($pos . ') ', 'NOTE') . $this->colorfulString(sprintf(" %-{$topLen}s", $it[0]), 'NOTE') . $it[1] . PHP_EOL;
        }

        return $text;
    }

    // 自定义帮助说明
    protected function getHelpText($text) {
        return $text;
    }

    protected function colorfulString($text, $type = NULL) {
        $colors = array(
            'WARNING'   => '1;33',
            'NOTE'      => '1;36',
            'SUCCESS'   => '1;32',
            'FAILURE'   => '1;35',
        );

        if (empty($type) || !isset($colors[$type])){
            return $text;
        }

        return "\033[" . $colors[$type] . "m" . $text . "\033[0m";
    }

}
