<?php
/**
 * Database Configuration
 * 
 * - support multi databases and tables
 * - support customing table routes
 * 
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

return array(
    /**
     * Database Servers
     */
    'servers' => array(
        'db_demo' => array(                         // server ID
            'host'      => 'localhost',             // database host
            'name'      => 'phalapi',               // database name
            'user'      => 'root',                  // database username
            'password'  => '',	                    // database password
            'port'      => '3306',                  // database port
            'charset'   => 'UTF8',                  // database charset
        ),
    ),

    /**
     * Customing Table Routes
     */
    'tables' => array(
        // Common Defatult Routes
        '__default__' => array(
            'prefix' => 'tbl_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_demo'),
            ),
        ),

        /**
        'demo' => array(                                                // table name
            'prefix' => 'tbl_',                                         // table prefix
            'key' => 'id',                                              // table primary key
            'map' => array(                                             // table route map
                array('db' => 'db_demo'),                               // single table: array('db' => server ID)
                array('start' => 0, 'end' => 2, 'db' => 'db_demo'),     // multi tables: array('start' => start pos, 'end' => end pos, 'db' => server ID)
            ),
        ),
         */
    ),
);
