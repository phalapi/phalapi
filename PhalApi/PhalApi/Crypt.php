<?php
/**
 * PhalApi
 *
 * An open source, light-weight API development framework for PHP.
 *
 * This content is released under the GPL(GPL License)
 *
 * @copyright   Copyright (c) 2015 - 2017, PhalApi
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        https://codeigniter.com
 */

/**
 * Crypt Interface
 *
 * @package     PhalApi\Crypt
 * @license     http://www.phalapi.net/license GPL GPL License
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-12-10
 */

interface PhalApi_Crypt {

    /**
     * Encrypt data with key
     * 
     * @param   mixed       $data   the data need to be encrypted
     * @param   string      $key    encrypt key
     * @return  mixed               encrypted data
     */
    public function encrypt($data, $key);
    
    /**
     * Decrypt data with key
     * 
     * @param   mixed       $data   encrypted data
     * @param   string      $key    encrypt key
     * @return  mixed               data after decrypt
     * @see     PhalApi_Crypt::encrypt()
     */
    public function decrypt($data, $key);
}
