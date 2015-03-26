<?php
/**
 * 拦截器接口
 *  
 * 为应用实现接口请求拦截提供统一处理接口
 *
 * @package PhalApi\Filter
 * @author dogstar <chanzonghuang@gmail.com> 2014-10-25
 */

interface PhalApi_Filter {

    public function check();
}
