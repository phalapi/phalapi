<?php
namespace Portal\Api;
use Portal\Common\Api;

use Portal\Domain\Plugin as PluginDomain;

/**
 * 插件应用
 * @ignore
 * @author dogstar 20200311
 */
class Plugins extends Api {

    public function getRules() {
        return array(
            'getMinePlugins' => array(
            ),
            'getMarketPlugins' => array(
                'searchParams' => array('name' => 'searchParams', 'type' => 'array', 'format' => 'json', 'default' => array(), 'desc' => '搜索条件'),
                'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'desc' => '第几页'),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => 20, 'min' => 1, 'max' => 1000, 'desc' => '分页数量'),
            ),
            'install' => array(
                'pluginKey' => array('name' => 'plugin_key', 'require' => true, 'regex' => '/^[0-9A-Za-z_]{1,}$/'),
            ),
            'uninstall' => array(
                'pluginKey' => array('name' => 'plugin_key', 'require' => true, 'regex' => '/^[0-9A-Za-z_]{1,}$/'),
            ),
        );
    }

    public function getMinePlugins() {
        $domain = new PluginDomain();
        return $domain->getMinePlugins();
    }

    public function getMarketPlugins() {
        $domain = new PluginDomain();
        $rs = $domain->getMarketPlugins($this->page, $this->limit, $this->searchParams);
        return $rs;
    }

    public function install() {
        set_time_limit(0);

        $domain = new PluginDomain();
        $detail = array();

        $installRs = $domain->install($this->pluginKey, $detail);

        return array('install_result' => $installRs,  'detail' => implode('<br />', $detail));
    }

    public function uninstall() {
        set_time_limit(0);

        $domain = new PluginDomain();
        $detail = array();

        $uninstallRs = $domain->uninstall($this->pluginKey, $detail);

        return array('uninstall_result' => $uninstallRs,  'detail' => implode('<br />', $detail));
    }
    
    public function marketTopContent() {
        $domain = new PluginDomain();
        $content = $domain->marketTopContent();
        return array('content' => $content);
    }
}
