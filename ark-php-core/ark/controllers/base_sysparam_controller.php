<?php

/**
 * sysparam.php
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
 * Description of sysparam
 *
 * @author angga
 */
class base_sysparam_controller extends app_crud_controller {

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields']    = array('sgroup', 'skey', 'svalue', 'lvalue');
        $config['names']    = array('Group', 'Key', 'Short Value', 'Long Value');
        return $config;
    }

}