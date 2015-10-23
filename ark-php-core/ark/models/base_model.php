<?php

/**
 * base_model.php
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

class base_model extends CI_Model {

    var $_name;
    var $_generated = array('id', 'org_id', 'status', 'created_time', 'created_by', 'updated_time', 'updated_by');
    var $_excluded = array();
    var $_dbname = 'default';
    var $_id = 'id';
    var $ci;

    function __construct() {
        parent::__construct();

        $this->ci = &get_instance();

        $this->_db($this->_dbname);

        $class_name = get_class($this);
        $pos = strrpos($class_name, '_');
        $this->_name = (($this->ci->config->item('use_db') && isset($this->ci->_db[$this->_dbname])) ? $this->ci->_db[$this->_dbname]->dbprefix : '') . strtolower(substr($class_name, 0, $pos));

        if (!$this->_db()) {
            show_error('No database configuration');
        }
    }

    function before_save(&$data, $id = null) {
        $CI = &get_instance();
        $user = $CI->auth->get_user();
        $now = date(l('format.mysql_datetime'));

        $user_id = (isset($user['id'])) ? $user['id'] : 0;

        if (empty($id)) {
            if (!isset($data['status'])) {
                $data['status'] = 1;
            }
            $data['created_time'] = $data['updated_time'] = $now;
            $data['created_by'] = $data['updated_by'] = $user_id;
        } else {
            $data['updated_time'] = $now;
            $data['updated_by'] = $user_id;
        }
    }

    function save($data, $id = null) {
        $this->hooks->call_hook($this->_name.':save', $data);
        $id = $this->_save($this->_name, $data, $id);
        $params = array(
            'data' => &$data,
            'id' => &$id,
        );
        $this->hooks->call_hook($this->_name.':post_save', $params);
        return $id;
    }

    function _save($tablename, $data, $id = null, $dbname = '') {
        $this->before_save($data, $id);

        $fields = $this->_db($dbname)->list_fields($tablename);
        $field_map = array();
        foreach ($fields as $field) {
            $field_map[$field] = $field;
        }
        foreach($data as $k => $v) {
            if (!isset($field_map[$k])) {
                unset($data[$k]);
            }
        }

        if (empty($id)) {
            $this->_db($dbname)->insert($tablename, $data);
        } else {
            $this->_db($dbname)->where($this->_id, $id);
            $this->_db($dbname)->update($tablename, $data);
        }

        $err_no = intval($this->_db($dbname)->_error_number());
        $err_msg = $this->_db($dbname)->_error_message();

        $id = (empty($id)) ? $this->_db($dbname)->insert_id() : $id;

        if (empty($err_no) || empty($id)) {
            return $id;
        } else {
            log_message('warn', $err_no . ' : ' . $err_msg);
            throw new RuntimeException($err_msg, $err_no);
        }
    }

    function get($filter = null) {
        $count = 0;
        if (!empty($filter) && !is_array($filter)) {
            $filter = array('id' => $filter);
        } elseif (is_array($filter)) {
            unset($filter['q']);
        }
        $result = $this->_db()->get_where($this->_name, $filter)->row_array();

        return $result;
    }

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
            $fields = $this->list_fields();
            if (!empty($fields['organization_id'])) {
                if (!empty($user['organization'])) {
                    $wheres[] = 'organization_id = ?';
                    $params[] = $user['organization'][0]['id'];
                } else {
                    $wheres[] = '0';
                }
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

    function listing($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        return $this->find($filter, $sort, $limit, $offset, $count);
    }

    function import($fields) {
        $this->_db()->select($fields);
        return $this->_db()->get($this->_name)->result_array();
    }

    function delete($id) {
        if (is_array($id)) {
            $this->_db()->where_in('id', $id)->delete($this->_name);
        } else {
            $this->_db()->where('id', $id)->delete($this->_name);
        }
    }

    function trash($id) {
        $data = array( 'status' => 0 );
        $this->before_save($data, $id);
        if (is_array($id)) {
            $this->_db()->where_in('id', $id)->update($this->_name, $data);
        } else {
            $this->_db()->where('id', $id)->update($this->_name, $data);
        }
    }

    function delete_detail($parent_field, $parent_id) {
        $this->_db()->delete($this->_name, array($parent_field => $parent_id));
    }

    function &_db($name = '') {
        if (empty($name)) {
            $name = $this->_dbname;
        }
        return db_conn($name);
    }

    function list_fields($exclude_ignore = false) {
        static $fields;
        static $excluding;

        if (!isset($fields)) {
            $field_data = $this->_db()->field_data($this->_name);
            foreach($field_data as $field) {
                $field = (array) $field;
                $fields[$field['name']] = $field;
            }

            $excluding = array();
            foreach($fields as $name => $field) {
                if (stripos($name, 'x_') !== 0 && !in_array($name, $this->_generated)) {
                    $excluding[$name] = $field;
                } else {
                    $this->_excluded = $name;
                }
            }
        }
        return ($exclude_ignore) ? $excluding : $fields;
    }

    function query($sql, $binds = FALSE, $return_object = TRUE) {
        return $this->_db()->query($sql, $binds, $return_object);
    }

    function truncate() {
        return $this->_db()->truncate($this->_name);
    }

    function &_model($name = '') {
        $CI = &get_instance();
        return $CI->_model($name);
    }

    function rebuild_tree($parent, $left) {
        // the right value of this node is the left value + 1
        $right = $left + 1;

        // get all children of this node
        $result = $this->_db()->where('parent', $parent)->get($this->_name)->result_array();
        foreach ($result as $row) {
            // recursive execution of this function for each
            // child of this node
            // $right is the current right value, which is
            // incremented by the rebuild_tree function
            $right = rebuild_tree($row['id'], $right);
        }

        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value
        $this->save(array(
            'lft' => $left,
            'rgt' => $right,
        ), $parent);

        // return the right value of this node + 1
        return $right + 1;
    }

}

