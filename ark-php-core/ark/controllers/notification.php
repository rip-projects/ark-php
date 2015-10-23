<?php

/**
 * notification.php
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

class notification extends app_crud_controller {
    var $_trail_excluded = array('fetch'=>true);

    function fetch($fetch_type = 'complete', $type = 1) {

        $user = $this->auth->get_user();

        $this->_data['data']['user_id'] = $user['id'];

        $sql = '
            SELECT
                n.*,
                CONCAT(uc.first_name, " ", uc.last_name) created_fullname
            FROM notification n
            JOIN user uc ON n.created_by = uc.id
            WHERE n.user_id = ? AND n.`type` = ? AND n.read_time IS NULL
            ORDER BY n.id DESC
            LIMIT 1
        ';
        $this->_data['data']['new_row'] = $this->db->query($sql, array($user['id'], $type))->row_array();
        if (empty($this->_data['data']['new_row'])) {
            $this->_data['data']['new_row'] = null;
        } else {
            $this->load->helper('text');
            $this->_data['data']['new_row']['message'] = word_limiter($this->_data['data']['new_row']['message'], 25);
        }

        if (!empty($this->_data['data']['new_row'])) {
            $sql = '
                SELECT COUNT(*) count FROM notification
                WHERE user_id = ? AND `type` = ? AND read_time IS NULL
            ';
            $this->_data['data']['row_count'] = $this->db->query($sql, array($user['id'], $type))->row()->count;
        }

        if ($fetch_type == 'rows' || $fetch_type == 'complete') {
            $this->load->helper('gravatar');

            $sql = '
                SELECT n.*, uc.email, uc.image, CONCAT(uc.first_name, " ", uc.last_name) created_fullname
                FROM notification n
                JOIN user u ON n.user_id = u.id
                JOIN user uc ON n.created_by = uc.id
                WHERE user_id = ? AND `type` = ?
                ORDER BY id DESC
                LIMIT 5
            ';

            $rows = $this->db->query($sql, array($user['id'], $type))->result_array();

            foreach ($rows as &$row) {
                if (!empty($row['icon']) && strpos($row['icon'], '://') === FALSE) {
                    $row['icon'] = site_url($row['icon']);
                }
                if (!empty($row['url']) && strpos($row['url'], '://') === FALSE) {
                    $row['url'] = site_url(str_replace('/edit/', '/detail/', $row['url']));
                }
                $row['icon'] =  get_image_path($row['image']);
            }

            $this->db->set('read_time', date('Y-m-d H:i:s'))
                ->where('read_time IS NULL', null, false)
                ->where('user_id', $user['id'])
                ->where('type', $type)
                ->update('notification');

            $this->_data['data']['rows'] = $rows;
        }

    }

    function _check_access() {
        $whitelist = array(
            'fetch',
        );
        if (in_array($this->uri->rsegments[2], $whitelist) && $this->auth->is_login()) {
            return true;
        }

        return parent::_check_access();
    }
}