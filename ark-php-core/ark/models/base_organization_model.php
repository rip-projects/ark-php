<?php

/**
 * base_organization_model.php
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

class base_organization_model extends app_base_model {
    function save_mine($data, $id = null) {
        $CI = &get_instance();
        $now = date('Y-m-d H:i:s');
        $user = $CI->auth->get_user();
        $new_id = $this->save($data, $id);
        if (empty($id)) {
            $uo = array(
                'user_id' => $user['id'],
                'org_id' => $new_id,
            );

            $this->before_save($uo);
            $this->_db()->insert('user_organization', $uo);

            $id = $new_id;
        } else {
            throw new Exception('unimplemented yet');
        }
        return $id;
    }

    function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $params = array();
        $where_str = '';
        $order_str = '';
        $limit_str = '';
        $wheres = array();
        $filter_wheres = array();
        $orders = array();

        $select_count = 'SELECT COUNT(id) count';
        $select = 'SELECT *';

        $wheres[] = 'status != 0';

        if (!empty($filter) && !is_array($filter)) {
            $wheres[] = 'id = ?';
            $params[] = $filter;
        } elseif (is_array($filter)) {
            unset($filter['q']);
            foreach($filter as $k => $f) {
                $filter_wheres[] = $k.' LIKE ?';
                $params[] = '%'.$this->db->escape_like_str($f).'%';
            }
            if (!empty($filter_wheres)) {
                $wheres[] = '('. implode(' OR ', $filter_wheres) .')';
            }
        }


        if (!empty($wheres)) {
            $where_str = ' WHERE '.implode(' AND ', $wheres);
        }

        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                $orders[] = $key.' '.(($value) ? $value : 'ASC');
            }
            $order_str = ' ORDER BY '.implode(', ', $orders);
        }

        $table_name = $this->_db()->_protect_identifiers($this->_db()->dbprefix . $this->_name);
        $sql = ' FROM '.$table_name.$where_str.$order_str;
        $count = $this->_db()->query($select_count.$sql, $params)->row()->count;

        if (!empty($limit)) {
            $limit_str = ' LIMIT ?, ?';
            $params[] = intval($offset);
            $params[] = intval($limit);
        }
        $result = $this->_db()->query($select.$sql.$limit_str, $params)->result_array();
        return $result;
    }

}