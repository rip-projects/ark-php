<?php

/**
 * Notif_sms.php
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

class Notif_sms extends CI_Driver {
    var $sms_url = '';
    var $sms_debug = false;
    var $sms_debug_phone = '';

    function _initialize($params) {
        $this->ci->load->library('curl');
        $this->sms_url = (empty($params['sms_url'])) ? '' : $params['sms_url'];
        $this->sms_debug = (empty($params['sms_debug'])) ? '' : $params['sms_debug'];
        $this->sms_debug_phone = (empty($params['sms_debug_phone'])) ? '' : $params['sms_debug_phone'];
    }

    function _cron($type) {
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

            if (empty($notification['data'])) {
                continue;
            }

            $notification['phone'] = $this->_phone_normalize($notification['phone']);
            if (empty($notification['phone'])) {
                return;
            }


            $url = sprintf($this->sms_url, $notification['data']['smsid'], rawurlencode($notification['phone']), rawurlencode($notification['message']));

            $x = $this->ci->curl->simple_get($url);

            $f = @fopen(APPPATH . 'logs/sms-' . (date('Y-m-d')) . '.log.php', 'a+');
            if ($f) {
                fputs($f, date('Y-m-d H:i:s').' ==> ['. (($x) ? 'OK' : 'FAILED') .'] '. $url."\n");
                fclose($f);
            }

            if ($x) {
                $updated = array( 'read_time' => date('Y-m-d H:i:s') );
                $this->ci->db->where('id', $notification['id'])->update('notification', $updated);
            }
        }
    }

    function _phone_normalize($phone) {
        $phone = explode(',', $phone);
        $phone = trim($phone[0]);
        $phone = trim(preg_replace('/[\s-+()]/', '', $phone));
        if (isset($phone[0]) && $phone[0] == '0') {
            $phone = '62'.substr($phone, 1);
        }
        $phone = trim($phone);

        if ($this->sms_debug && !empty($this->sms_debug_phone)) {
            if ($phone != $this->sms_debug_phone) {
                return '';
            }
        }
        return $phone;
    }

    function notify($data) {

        $data['user']['phone'] = $this->_phone_normalize($data['user']['phone']);
        if (empty($data['user']['phone'])) {
            return;
        }

        $data['message'] = $this->ci->load->view($data['template'] . '/'. $data['type'], $data['data'], true);

        if (empty($data['user_id'])) {
            $data['user_id'] = (empty($data['user']['id'])) ? $data['user'] : $data['user']['id'];
            unset($data['user']);
        }

        $data['data'] = (empty($data['config'][$data['type']])) ? '' : json_encode($data['config'][$data['type']]);

        $this->ci->_model('notification')->save($data);
    }

}