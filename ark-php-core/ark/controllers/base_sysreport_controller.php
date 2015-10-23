<?php

/**
 * sysreport.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

/**
 * Description of sysreport
 *
 * @author jafar
 */
class base_sysreport_controller extends app_crud_controller {

    function report() {

        // FIXME reekoheek harus ada generate token untuk ngirim report kalau ga bisa diflood
        if (!empty($_POST)) {
            $this->_model()->save($_POST);
        }
        exit;
    }

    function _check_access() {
        if ($this->uri->rsegments[2] == 'report') {
            return true;
        }
        return parent::_check_access();
    }
}
