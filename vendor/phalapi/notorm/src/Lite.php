<?php
namespace PhalApi\NotORM;

/**
 * 轻量级入口
 *
 * - 原来的NotORM是过程式的写法，在composer下不管需要与否都加载文件，影响性能
 * - 现在保持原来的实现，增加一个OOP的使用入口，从而使用到时再自动加载
 */

require_once dirname(__FILE__) . '/NotORM.php';

class Lite extends \NotORM {
}
