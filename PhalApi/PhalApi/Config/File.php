<?php 
/**
 * PhalApi_Config_File 文件配置类
 *
 * - 从配置文件获取参数配置
 * 
 * 使用示例：
 *      $config = new PhalApi_Config_File('./Config');
 *      $config->get('sys.db.user');
 *
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class PhalApi_Config_File implements PhalApi_Config {

	private $path = '';
	private $map = NULL;
	
	public function __construct($configPath) {
		$this->path = $configPath;
	}
	
	/**
     * 获取配置
     * 首次获取时会进行初始化
     *
     * @param $key string 配置键值
     * @return mixed 需要获取的配置值
     * @author dogstar 2012-12-23
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
     * @return 
     * @author	dogstar 2012-12-23
     */
	private function loadConfig($fileName) {
		$config = include($this->path . DIRECTORY_SEPARATOR . $fileName . '.php');
		
		$this->map[$fileName] = $config;
	}
}
