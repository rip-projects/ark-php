<?php

/**
 * app_base_controller.php
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

if (!class_exists('app_base_controller')) {

    class app_base_controller extends base_controller {

        function _post_controller_constructor() {
            parent::_post_controller_constructor();
            // FIXME this should be plugin
            // if (!isset($this->admin_panel)) {
            //     $this->load->library('xmenu', null, 'admin_panel');
            // }
        }
    }

}

