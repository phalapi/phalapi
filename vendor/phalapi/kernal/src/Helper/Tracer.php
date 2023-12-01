<?php
namespace PhalApi\Helper;

/**
 * Tracer 全球追踪器类
 *     
 * 用于调试，追踪接口执行的情况
 *
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      喵了个咪<wenzhenxi@vip.qq.com> 2017-04-15
 * @author      dogstar <chanzonghuang@gmail.com> 2017-04-15
 */

class Tracer {

    /**
     * @var array $timeline 时间线
     */
    protected $timeline = array();

    /**
     * @var array $sqls 所执行的SQL语句
     */
    protected $sqls = array();

    /**
     * 日志服务
     */
    protected $logger;

    public function __construct($logger = NULL) {
        $this->logger = $logger ? $logger : \PhalApi\DI()->logger;
    }

    /**
     * 打点，纪录当前时间点
     * @param string $tag 当前纪录点的名称，方便最后查看路径节点
     * @return NULL
     */
    public function mark($tag = NULL) {
        if (!\PhalApi\DI()->debug) {
            return;
        }

        $backTrace = debug_backtrace();
        if (empty($this->timeline)) {
            array_shift($backTrace);
        }
        // TODO 关于追踪，看下如何追踪更合适

        $this->timeline[] = array(
            'tag' => $tag, 
            'time' => $this->getCurMicroTime(),
            'file' => isset($backTrace[0]['file']) ? $backTrace[0]['file'] : '',
            'line' => isset($backTrace[0]['line']) ? $backTrace[0]['line'] : 0,
            'memory' => $this->getMemoryUsage(), 
        );
    }

    /**
     * 生成报告
     * @return array
     */
    public function getStack() {
        $stack = array();

        $preMicroTime = NULL;
        foreach ($this->timeline as $index => $item) {
            if ($preMicroTime === NULL) {
                $preMicroTime = $item['time'];
            }
            $internalTime = $item['time'] - $preMicroTime;
            $internalTime = round($internalTime/10, 1);

            $stack[] = sprintf('[#%d - %sms - %s%s]%s(%d)',
                $index + 1, 
                $internalTime, 
                $item['memory'],
                $item['tag'] !== NULL ? ' - ' . $item['tag'] : '', 
                $item['file'], 
                $item['line']
            );
        }

        return $stack;
    }

    /**
     * 获取当前毫秒时间
     * @return float
     */
    protected function getCurMicroTime() {
        return round(microtime(true) * 10000);
    }

    /**
     * 获取内存使用
     * @param boolean $realUsage 为true时表示获取系统分配总的内存尺寸（不管是否使用）；为false时获取实际使用的内存量
     * @return string 格式化的内存大小，保留一位小数点，如：120.5MB
     */
    protected function getMemoryUsage($realUsage = false) {
        $size = memory_get_usage($realUsage);
        $unit = array('B','KB','MB','GB','TB','PB');
        $i = floor(log($size, 1024));
        $str = round($size/pow(1024, $i), 1) . $unit[$i];
        return $str;
    }

    /**
     * 纪录SQL语句
     * @param string $string  SQL语句
     * @return NULL
     */
    public function sql($statement) {
        $di = \PhalApi\DI();
        $this->sqls[] = $statement;

        // 只提取部分必要的参数，避免全部记录，以及避免记录密码等敏感信息到日志文件
        $request = array(
            'service' => $di->request->getService(),
        );

        // 保存到日志
        if ($di->config->get('sys.enable_sql_log')) {
            $this->logger->log('SQL', $statement, array('request' => $request));
        }
    }

    /**
     * 获取SQL语句
     * @return array
     */
    public function getSqls() {
        return $this->sqls;
    }

    /**
     * 返回最后一条SQL语句
     * @return string|false 没有任何SQL语句时返回false
     */
    public function getLastSql() {
        return end($this->sqls);
    }
}
