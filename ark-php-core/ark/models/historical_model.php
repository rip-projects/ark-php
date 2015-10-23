<?php

/**
 * historical_model.php
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

class historical_model extends app_base_model {
    var $_history = '';

    function __construct() {
        parent::__construct();

        if ($this->_history === '') {
            $this->_history = $this->_name . '_hist';
        }
    }

    function save($data, $id = null) {
        $activity = (empty($id)) ? 'insert' : 'update';
        $id = parent::save($data, $id);

        $data = $this->get($id);
        $data['activity'] = $activity;
        $this->save_history($data, $id);

        return $id;
    }

    function trash($id) {
        parent::trash($id);

        if (!is_array($id)) {
            $id = array($id);
        }
        foreach ($id as $i) {
            $data = $this->get($i);
            $this->save_history($data, $i);
        }
    }

    function delete($id) {
        if (!is_array($id)) {
            $id = array($id);
        }

        $data = $this->_db()->where_in('id', $id)->get($this->_name)->result_array();

        parent::delete($id);

        $now = date('Y-m-d H:i:s');
        $user = $this->auth->get_user();
        foreach($data as $row) {
            $row['activity'] = 'delete';
            $row['updated_time'] = $now;
            $row['updated_by'] = $user['id'];
            $row['status'] = '-1';

            $this->save_history($row, $row['id']);
        }
    }

    function save_history($data, $id) {
        if (empty($data['activity'])) {
            $trace = debug_backtrace();
            $data['activity'] = $trace[1]['function'];
        }

        $data['object_id'] = $id;
        unset($data['id']);

        $this->_db()->insert($this->_history, $data);
    }
}