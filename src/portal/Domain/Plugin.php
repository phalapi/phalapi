<?php
namespace Portal\Domain;

/**
 * 插件应用
 * @author dogstar <huangchanzong@yesapi.cn> 20200311
 */

class Plugin {

    public function uninstall($pluginKey, &$detail = array()) {
        $detail[] = '正在卸载 '. $pluginKey;

        if (!$this->installCheckInstalled($pluginKey, $detail)) {
            $detail[] = '插件未安装，无须卸载';
            return false;
        }

        // 读取插件信息
        $detail[] = '开始卸载插件……';
        $jsonFile = $this->getJsonFile($pluginKey);
        $jsonArr = json_decode(file_get_contents($jsonFile), true);
        $detail[] = sprintf('插件：%s（%s），开发者：%s，版本号：%s', $jsonArr['plugin_key'], $jsonArr['plugin_name'], $jsonArr['plugin_author'], $jsonArr['plugin_version']);

        $plugin_files = $jsonArr['plugin_files'];
        foreach (array($plugin_files['config'], $plugin_files['plugins'], $plugin_files['data']) as $f) {
            $af = API_ROOT . '/' . $f;
            if (file_exists($af) && unlink($af)) {
                $detail[] = '已删除：' . $f;
            } else {
                $detail[] = '删除失败：' . $f;
            }
        }

        $detail[] = '开始删除src源代码……';
        $this->removeSource($plugin_files['src']);

        $detail[] = '开始删除public源代码……';
        $this->removeSource($plugin_files['public']);

        $detail[] = '删除插件json配置……';
        unlink($jsonFile);

        $detail[] = '卸载完毕！';

        return true;
    }

    protected function removeSource($paths, &$detail = array()) {
        if (is_array($paths)) {
            foreach ($paths as $f) {
                $detail[] = '开始删除：' . $f;
                $af = API_ROOT . '/' . $f;
                if (is_dir($af)) {
                    $this->recursiveDelete($af);
                } else {
                    unlink($af);
                }
            }
        }
    }

    protected function recursiveDelete($dir) {    
        // 打开指定目录
        if ($handle = @opendir($dir))
        {
            while (($file = readdir($handle)) !== false)
            {
                if (($file == ".") || ($file == ".."))
                {
                    continue;
                }
                if (is_dir($dir . '/' . $file))
                {
                    // 递归
                    recursiveDelete($dir . '/' . $file);
                }
                else
                {
                    unlink($dir . '/' . $file); // 删除文件
                }
            }
            @closedir($handle);
            rmdir ($dir); 
        }
    }

    /**
     * 安装应用插件
     * @param string $pluginKey 插件应用的名称
     * @param string $detail 安装信息收集
     * @return boolean 安装成功与否
     */
    public function install($pluginKey, &$detail = array(), $isReinstall = true) {
        $detail[] = '正在安装 '. $pluginKey;

        // 检测插件应用是否存在
        $detail[] = '开始检测插件安装包 ' . $pluginKey;
        if (!$this->installCheckExists($pluginKey, $detail)) {
            return false;
        }

        // 检测插件是否已安装
        $detail[] = '检测插件是否已安装';
        if ($this->installCheckInstalled($pluginKey, $detail) && !$isReinstall) {
            return false;
        }

        // 开始安装
        // 解压源代码
        $detail[] = '开始安装插件……';
        $zipFile = $this->getZipFile($pluginKey);
        $zip = new \ZipArchive();//新建一个ZipArchive的对象
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo(API_ROOT);
            $zip->close();
        } else {
            $detail[] = '插件解压失败，请检测压缩是否已下载完整。';
            return false;
        }

        // 读取插件信息
        $detail[] = '检测插件安装情况……';
        $jsonFile = $this->getJsonFile($pluginKey);
        if (!$this->installCheckInstalled($pluginKey, $detail)) {
            // 安装失败
            $detail[] = '插件安装失败，无法找到json配置文件。请确保目录有写入权限，解决方法：进行环境预热：php ./bin/phalapi-plugin-prepare.php，或使用命令安装：php ./bin/phalapi-plugin-install.php。';
            return false;
        }

        $jsonArr = json_decode(file_get_contents($jsonFile), true);
        $detail[] = sprintf('插件：%s（%s），开发者：%s，版本号：%s，安装完成！', $jsonArr['plugin_key'], $jsonArr['plugin_name'], $jsonArr['plugin_author'], $jsonArr['plugin_version']);

        // 检测环境依赖、composer依赖和PHP扩展依赖
        $detail[] = '开始检测环境依赖、composer依赖和PHP扩展依赖';
        if (!$this->installCheckDepends($jsonArr['plugin_depends'], $detail)) {
            return false;
        }

        // 进行数据库变更，例如添加菜单，创建新表
        $detail[] = '开始数据库变更……';
        if (!$this->installDatabaseUpgrade($pluginKey, $detail)) {
            return false;
        }

        // 返回结果
        $detail[] = '插件安装完毕！';

        return true;
    }

    protected function installCheckExists($pluginKey, &$detail = []) {
        $zipFile = $this->getZipFile($pluginKey);
        if (!file_exists($zipFile)) {
            $detail[] = '插件安装包不存在：' . 'plugins/' . $pluginKey . '.zip';
            return false;
        }
        return true;
    }

    protected function getZipFile($pluginKey) {
        return API_ROOT . '/plugins/' . $pluginKey . '.zip';
    }

    protected function installCheckInstalled($pluginKey, &$detail = []) {
        $jsonFile = $this->getJsonFile($pluginKey);
        if (file_exists($jsonFile)) {
            $detail[] = '插件已安装：' . 'plugins/' . $pluginKey . '.json';
            return true;
        }
        return false;
    }

    protected function getJsonFile($pluginKey) {
        return API_ROOT . '/plugins/' . $pluginKey . '.json';
    }

    // 只作提示
    protected function installCheckDepends($plugin_depends, &$detail = []) {
        if (isset($plugin_depends['PHP'])) {
            $detail[] = sprintf('PHP版本需要：%s，当前为：%s', $plugin_depends['PHP'], PHP_VERSION);
        }
        if (isset($plugin_depends['MySQL'])) {
            $detail[] = sprintf('MySQL版本需要：%s', $plugin_depends['MySQL']);
        }
        if (isset($plugin_depends['PhalApi'])) {
            $detail[] = sprintf('PhalApi版本需要：%s，当前为：%s', $plugin_depends['PhalApi'], PHALAPI_VERSION);
        }
        if (!empty($plugin_depends['composer'])) {
            foreach ($plugin_depends['composer'] as $pkg => $v) {
                $detail[] = sprintf('composer需要 %s %s', $pkg, $v); 
            }
        }
        if (!empty($plugin_depends['extension'])) {
            $detail[] = sprintf('php扩展需要：' . implode('，', $plugin_depends['extension']));
        }
        return true;
    }

    protected function installDatabaseUpgrade($pluginKey, &$detail = []) {
        $sqlFile = $this->getDataSqlFile($pluginKey);
        if (!file_exists($sqlFile)) {
            $detail[] = '无数据库变更';
            return true;
        }

        // 兼容windows的换行
        $sqlContent = str_replace(";\r\n", ";\n", file_get_contents($sqlFile));
        $sqlArr = explode(";\n", $sqlContent);

        // 待进行替换的表名，以便加上当前表前缀
        $tablePrefix = \PhalApi\DI()->config->get('dbs.tables.__default__.prefix');

        foreach ($sqlArr as $sql) {
            $sql = trim($sql);
            if (empty($sql)) {
                continue;
            }
            try {
                // 表前缀的处理
                $sql = str_replace($pluginKey, $tablePrefix . $pluginKey, $sql);

                \PhalApi\DI()->notorm->demo->executeSql($sql);
                $detail[] = $sql;
            } catch (\PDOException $ex) {
                $detail[] = '数据库执行失败：' . $sql . '。失败原因：' . $ex->getMessage();
            }
        }

        return true;
    }

    protected function getDataSqlFile($pluginKey) {
        return API_ROOT . '/data/' . $pluginKey . '.sql';
    }

    public function getMarketPlugins($page = 1, $perpage = 20, $searchParams = array()) {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $url = 'https://www.phalapi.net/plugins.php?' . http_build_query(array('page' => $page, 'perpage' => $perpage, 'searchParams' => json_encode($searchParams), 'host' => $host, 'version' => PHALAPI_VERSION));
        $curl = new \PhalApi\CUrl();
        $result = $curl->get($url, 10000);
        $result = json_decode($result, true);

        $total = isset($result['total']) ? $result['total'] : 0;;
        $items = isset($result['plugins']) ? $result['plugins'] : array();

        // 加载已安装插件
        $mineKeys = array();
        foreach (glob(API_ROOT . '/plugins/*.json') as $file) {
            $jsonArr = json_decode(file_get_contents($file), true);
            $mineKeys[$jsonArr['plugin_key']] = $jsonArr['plugin_version'];
        }

        // 加载未安装的
        $downKeys = array();
        foreach (glob(API_ROOT . '/plugins/*.zip') as $file) {
            $fileArr = explode('plugins', $file);
            $fileArr = explode('.zip', $fileArr[1]);
            $pluginKey = !empty($fileArr[0]) ? trim($fileArr[0], '/') : '';
            if (empty($pluginKey) || isset($items[$pluginKey])) {
                continue;
            }
            $downKeys[] = $pluginKey;
        }

        foreach ($items as &$itRef) {
            if (isset($mineKeys[$itRef['plugin_key']])) {
                // 已安装
                $itRef['plugin_status'] = 1;
                if (version_compare($itRef['plugin_version'], $mineKeys[$itRef['plugin_key']], '>')) {
                    // 待升级
                    $itRef['plugin_status'] = 3;
                    $itRef['plugin_version'] = $itRef['plugin_version'] . '(当前:' . $mineKeys[$itRef['plugin_key']] . ')';
                }
            } else if ($itRef['plugin_status'] != 1 && in_array($itRef['plugin_key'], $downKeys)) {
                // 已下载，未安装
                $itRef['plugin_status'] = 2;
            }
        }

        return array('total' => $total, 'items' => $items);
    }

    public function getMinePlugins() {
        $total = 0;
        $items = array();

        // 加载已安装插件
        foreach (glob(API_ROOT . '/plugins/*.json') as $file) {
            $jsonArr = json_decode(file_get_contents($file), true);
            $items[$jsonArr['plugin_key']] = array(
                'plugin_key' => $jsonArr['plugin_key'],
                'plugin_name' => $jsonArr['plugin_name'],
                'plugin_author' => $jsonArr['plugin_author'],
                'plugin_version' => $jsonArr['plugin_version'],
                'plugin_status' => 1,
            );
        }

        // 加载未安装的
        foreach (glob(API_ROOT . '/plugins/*.zip') as $file) {
            $fileArr = explode('plugins', $file);
            $fileArr = explode('.zip', $fileArr[1]);
            $pluginKey = !empty($fileArr[0]) ? trim($fileArr[0], '/') : '';
            if (empty($pluginKey) || isset($items[$pluginKey])) {
                continue;
            }
            $items[$pluginKey] = array(
                'plugin_key' => $pluginKey,
                'plugin_name' => '-',
                'plugin_author' => '-',
                'plugin_status' => 0,
            );                                

        }

        $items = array_values($items);
        $total = count($items);

        return array('total' => $total, 'items' => $items);
    }
    
    public function getMinePluginsInstallNum() {
        $total = 0;
        // 加载已安装插件
        foreach (glob(API_ROOT . '/plugins/*.json') as $file) {
            $total ++;
        }
        return $total;
    }

    public function marketTopContent() {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $curl = new \PhalApi\CUrl();
        $result = $curl->get('https://www.phalapi.net/plugins_hot.php', 10000);
        $result = json_decode($result, true);
        $moreContent = !empty($result['hot']) ? $result['hot'] : '';

        $content = sprintf('<blockquote class="layui-elem-quote">当前网站域名是：%s，PhalApi版本是：v%s。
            更多精品插件和优质应用，尽在<a href="%s" target="_blank"  class="layui-btn layui-btn-normal layui-btn-sm ">PhalApi插件仓库</a>。%s</blockquote>', 
            $host, PHALAPI_VERSION, 'https://gitee.com/dogstar/PhalApi-Net/tree/master/download/plugins', $moreContent);

        return $content;
    }
}
