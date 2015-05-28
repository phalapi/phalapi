<?php 
/**
 * PhalApi_Config_File 文件配置类
 *
 * <li>从配置文件获取参数配置</li>
 * 
 * 使用示例：
 * <br>
 * <code>
 * 		$config = new PhalApi_Config_File('./Config');
 * 		$config->get('sys.db.user');
 * </code>
 *
 * @package     PhalApi\Config
 * @see         PhalApi_Config::get()
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_Config_File implements PhalApi_Config {

	/**
	 * @var string $path 配置文件的目录位置
	 */
	private $path = '';
	
	/**
	 * @var array $map 配置文件的映射表，避免重复加载 
	 */
	private $map = array();
	
	public function __construct($configPath) {
		$this->path = $configPath;
	}
	
	/**
     * 获取配置
     * 首次获取时会进行初始化
     *
     * @param $key string 配置键值
     * @return mixed 需要获取的配置值
     */
	public function get($key, $default = NULL) {
		$keyArr = explode('.', $key);
		$fileName = $keyArr[0];
		
		if (!isset($this->map[$fileName])) {
			$this->loadConfig($fileName);
		}
		
		$rs = NULL;
		$preRs = $this->map;
		foreach ($keyArr as $subKey) {
			if (!isset($preRs[$subKey])) {
				$rs = NULL;
				break;
			}
			$rs = $preRs[$subKey];
			$preRs = $rs;
		}
		
		return $rs !== NULL ? $rs : $default;
	}
	
	/**
     * 加载配置文件
     * 加载保存配置信息数组的config.php文件，若文件不存在，则将$map置为空数组
     *
     * @param string $fileName 配置文件路径
     * @return array 配置文件对应的内容
     */
	private function loadConfig($fileName) {
		$config = include($this->path . DIRECTORY_SEPARATOR . $fileName . '.php');
		
		$this->map[$fileName] = $config;
	}
}
