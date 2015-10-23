<?php

/**
 * install.php
 *
 * @package     ark-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

$config = array(
    'install_ftp_username' => 'root',
    'install_ftp_password' => 'password',
    'install_ftp_hostname' => 'localhost',
    'install_ftp_base_dir' => '/public_html/',
    'install_excluded' => array(
        '-X .*',
        '-x ^index.php',
        '-x ^.git',
        '-x ^nbproject',
        '-x ^error_log',
        '-x ^application/cache',
        '-x ^application/logs',
        '-x ^application/third_party',
        '-x ^data',
        '-x ^demo',
        '-x ^design',
        '-x ^sources',
        '-x ^install',

    ),
);


