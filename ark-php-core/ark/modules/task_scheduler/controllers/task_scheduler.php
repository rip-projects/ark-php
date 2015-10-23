<?php

/**
 * task_scheduler.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
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
 * Description of task_scheduler
 *
 * @author jafar
 */
class task_scheduler extends App_Crud_Controller {

    function _config_grid() {
        $config = parent::_config_grid();
        $config['formats'] = array();
        return $config;
    }

    function _save($id = null) {
        if ($_POST) {
            $_POST['next_time'] = $this->_model()->get_next_time($_POST);
        }
        parent::_save($id);
    }

    function cron() {
        $this->_model()->cron();
        exit;
    }

}
