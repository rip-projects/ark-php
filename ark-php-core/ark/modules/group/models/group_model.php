<?php

/**
 * group_model.php
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

class group_model extends app_base_model {

    function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $params = array();
        $where_str = '';
        $order_str = '';
        $limit_str = '';
        $wheres = array();
        $filter_wheres = array();
        $orders = array();

        $select_count = 'SELECT COUNT(*) count';
        $select = 'SELECT *';

        $user = $this->auth->get_user();
        if (!$user['is_top_member']) {
            if (!empty($user['organization'])) {
                $wheres[] = 'organization_id = ?';
                $params[] = $user['organization'][0]['id'];
            } else {
                $wheres[] = '0';
            }
        }

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

    function get_organizations($id) {
        return $this->db->select('organization.*, group_data.id, organization.id org_id')
            ->where('group_id', $id)
            ->join('group_data', 'organization.id = group_data.org_id')
            ->get('organization')
            ->result_array();
    }

    function add_organization($id, $org_id) {
        $data = array(
            'group_id' => $id,
            'org_id' => $org_id,
        );
        $row = $this->_db()->where($data)->get('group_data')->row_array();
        if (!empty($row)) {
            return;
        }

        parent::before_save($data);
        $this->_db()->insert('group_data', $data);
    }

    function delete_organization($id, $org_id) {
        $this->_db()->where('id', $org_id);
        $this->_db()->delete('group_data');
    }

    function user_group_data() {
        $user = $this->auth->get_user();
        if ($user['is_top_member']) {
            $gd = $this->db->where('id !=', 1)->get('organization')->result_array();
        } else {
            $gd = $user['group_data'];
        }
        return $gd;
    }
}