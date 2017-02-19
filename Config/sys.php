<?php 
/**
 * System-level configuration, usually placed in different environments under different configurations
 */

return array(
	/**
	 * Default Environment Config
	 */
	'debug' => false,

	/**
	 * MC Cache Config
	 */
	 'mc' => array(
        'host' => '127.0.0.1',
        'port' => 11211,
	 ),

    /**
     * Encryption
     */
    'crypt' => array(
        'mcrypt_iv' => '12345678',      // 8 characters
    ),
);
