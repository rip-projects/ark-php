<?php

/**
 * sys.php
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

class base_sys_controller extends app_base_controller {

    function error() {
        $sq = (empty($_GET['continue'])) ? '' : '?'.$_GET['continue'];
        redirect('user/login'.$qs);
    }

    function set_lang($lang) {
        $this->lang->set_language($lang);
        redirect($_SERVER['HTTP_REFERER']);
    }

    function cache_clean() {
        $this->cache->clean();
    }

    function _check_access() {
        $allows = array(
            'error',
            'set_lang',
        );
        if (in_array($this->uri->rsegments[2], $allows)) {
            return true;
        }
        return parent::_check_access();
    }

}