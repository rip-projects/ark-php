<?php

/**
 * base_role_model.php
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

class base_role_model extends app_base_model {

    var $CACHE_KEY_PRIVILEGE = 'ROLE_PRIVILEGE';
    var $CACHE_TIMEOUT_PRIVILEGE = 86400;

    function add_role_user($role_id, $user_id) {
        $role_user = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
        );
        $gu = $this->db->get_where('user_role', $role_user)->row_array();

        if (empty($gu)) {
            $this->before_save($role_user);
            $this->db->insert('user_role', $role_user);
        }
    }

    function get_privileges($role_id) {
        return $this->_db()->where('role_id', $role_id)->get('privilege_role')->result_array();
    }

    function privilege($uri, $role_id = '') {
        $CI = &get_instance();

        $uri = explode('?', $uri);
        $uri = $uri[0];

        $uri = explode('/', $uri);
        $controller = $uri[0];

        $uri = array_slice($uri, 0, 2);
        $uri = implode('/', $uri);

        $privileges = $CI->cache->context_get($this->CACHE_KEY_PRIVILEGE);

        if ($privileges === FALSE) {
            $privileges = array();
            $result = $this->_db()->where('status', 1)->get('privilege_role')->result_array();
            foreach ($result as $row) {
                $privileges[$row['role_id']][$row['uri']] = 1;
            }
            $CI->cache->context_save($this->CACHE_KEY_PRIVILEGE, $privileges, $this->CACHE_TIMEOUT_PRIVILEGE);
        }

        $user = $CI->auth->get_user();

        if (empty($role_id)) {
            $roles = (isset($user['roles'])) ? $user['roles'] : array();
            foreach ($roles as $role) {
                $role_id = $role['role_id'];
                if (isset($privileges) && isset($privileges[$role_id]) &&
                    (isset($privileges[$role_id]['*']) || isset($privileges[$role_id][$controller.'/*']) || isset($privileges[$role_id][$uri]))) {
                    return true;
                }
            }
        } else {
            if (isset($privileges) && isset($privileges[$role_id]) && (isset($privileges[$role_id]['*']) || isset($privileges[$role_id][$uri]))) {
                return true;
            }
        }

        return false;
    }

    function add_privilege($id, $uri) {
        $data = array(
            'role_id' => $id,
            'uri' => $uri,
        );
        $row = $this->_db()->where($data)->get('privilege_role')->row_array();
        if (!empty($row)) {
            return;
        }

        parent::before_save($data);
        $this->_db()->insert('privilege_role', $data);
        $this->cache->context_delete($this->CACHE_KEY_PRIVILEGE);
    }

    function delete_privilege($id, $priv_id) {
        $this->_db()->where('id', $priv_id);
        $this->_db()->delete('privilege_role');
        $this->cache->context_delete($this->CACHE_KEY_PRIVILEGE);
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

        $wheres[] = 'status > 0';

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