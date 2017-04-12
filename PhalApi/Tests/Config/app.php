<?php

return array(
    'apiCommonRules' => array(
        'from' => array('name' => 'from', 'default' => 'phpunit'),
    ),

    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*            通配，全部接口服务，慎用！
     * - Default.*      Api_Default接口类的全部方法
     * - *.Index        全部接口类的Index方法
     * - Default.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => array(
        '*.Index',
        'ServiceWhitelist.PoPo',
    ),
);
