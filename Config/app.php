<?php
/**
 * Please add your config here as you need
 */

return array(

    /**
     * API common param rules
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),

    /**
     * Service whilelist, format: SERVICE_CLASS_NAME.SERVICE_METHOD_NAME
     *
     * Examples:
     * - *.*            Matct all, BE CAREFULL!
     * - Default.*      All the methods in class ```Api_Default```
     * - *.Index        All method ```index``` in any class
     * - Default.Index  Specified service, i.e. ```Api_Default::Index()```
     */
    'service_whitelist' => array(
        'Default.Index',
    ),
);
