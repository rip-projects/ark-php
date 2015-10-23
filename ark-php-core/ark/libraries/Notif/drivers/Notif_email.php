<?php

/**
 * Notif_email.php
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

class Notif_email extends CI_Driver {
    function _cron($type) {
        $this->ci->load->library('xmailer');

        $sql = '
            SELECT u.*, n.*
            FROM notification n
            JOIN user u ON n.user_id = u.id
            WHERE type = ?
            AND read_time IS NULL
        ';

        $notifications = $this->ci->db->query($sql, array( $type, ))->result_array();
        foreach ($notifications as $notification) {
            $notification['data'] = (empty($notification['data'])) ? '' : json_decode($notification['data'], true);
            if (!empty($notification['data'])) {
                $this->ci->xmailer->initialize($notification['data']);
            }
            $this->ci->xmailer->subject = $notification['title'];
            $this->ci->xmailer->body = $notification['message'];
            $this->ci->xmailer->send('', '', $notification['email']);

            $updated = array( 'read_time' => date('Y-m-d H:i:s') );
            $this->ci->db->where('id', $notification['id'])->update('notification', $updated);
        }
    }

    function notify($data) {
        if (empty($data['user']['email']) /*|| $data['user']['email'] != 'reekoheek@gmail.com'*/) {
            return;
        }
        $message = $this->ci->load->view($data['template'] . '/'. $data['type'], $data['data'], true);
        $message = explode("\n", $message, 2);

        $data['title'] = trim($message[0]);
        $data['message'] = trim($message[1]);

        $data['data'] = (empty($data['config'][$data['type']])) ? '' : json_encode($data['config'][$data['type']]);


        if (empty($data['user_id'])) {
            $data['user_id'] = (empty($data['user']['id'])) ? $data['user'] : $data['user']['id'];
            unset($data['user']);
        }
        $this->ci->_model('notification')->save($data);
    }

}
