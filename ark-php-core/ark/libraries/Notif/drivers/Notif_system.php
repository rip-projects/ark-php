<?php

/**
 * Notif_system.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2012/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (yyyy/mm/dd hh:mm:ss) (author)
 * 2012/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class Notif_system extends CI_Driver {
    function show() {
        $data = array(
            'self' => $this,
        );
        return $this->ci->load->view('libraries/notif_system_show', $data, true);
    }

    function notify($data) {
        if (empty($data['user_id'])) {
            $data['user_id'] = (empty($data['user']['id'])) ? $data['user'] : $data['user']['id'];
            unset($data['user']);
        }


        if (empty($data['title'])) {
            $message = $this->ci->load->view($data['template'] . '/'. $data['type'], $data['data'], true);
            $message = explode("\n", $message, 2);
            $data['message'] = $data['title'] = $message[0];
            if (count($message) == 2) {
                $data['message'] = $message[1];
            }
        }
        unset($data['data']);
        $this->ci->_model('notification')->save($data);
    }
}