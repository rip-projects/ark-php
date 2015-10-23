<?php

/**
 * menu_model.php
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

class Menu_model extends App_Base_Model {

    var $CACHE_KEY = 'MENU';
    var $CACHE_TIMEOUT = 86400;

    function _find_menu($parent_id = 0) {
        $CI = &get_instance();

        $menu_rows = $CI->cache->context_get($this->CACHE_KEY);
        if ($menu_rows === FALSE) {
            $result = $this->_db()->where('status', 1)->order_by('position, id')->get('menu')->result_array();
            $menu_rows = array();
            foreach ($result as $row) {
                $menu_rows[$row['parent_id']][] = $row;
            }
            $CI->cache->context_save($this->CACHE_KEY, $menu_rows, $this->CACHE_TIMEOUT);
        }

        $menus = array();
        $t_menu = (empty($menu_rows[$parent_id])) ? array() : $menu_rows[$parent_id];
        foreach ($t_menu as $menu) {
            if (empty($menu['uri']) || $CI->_model('user')->privilege($menu['uri'])) {
                $menu['children'] = $this->_find_menu($menu['id']);
                if (!empty($menu['children']) || $menu['uri'] != '') {
                    $menus[] = $menu;
                }
            }
        }
        return $menus;
    }

    function find_admin_panel() {
        $CI = &get_instance();
        return $this->_find_menu();
    }

    function find_children($parent_id = 0, $level = 0) {
        $retval = array();
        $result = $this->_db()->query('SELECT * FROM ' . $this->_db()->dbprefix . 'menu WHERE parent_id = ? ORDER BY position', $parent_id)->result_array();
        foreach ($result as $row) {
            $row['title'] = str_repeat('___', $level) . ' ' . $row['title'];
            $retval[] = $row;
            $sub_result = $this->find_children($row['id'], $level + 1);

            if (!empty($sub_result)) {
                $retval = array_merge($retval, $sub_result);
            }
        }
        return $retval;
    }

    function find_hierarchical($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $result = $this->find_children();
        return $result;
    }

    function delete($id) {
        $result = parent::delete($id);
        $this->cache->context_delete($this->_model()->CACHE_KEY);
        return $result;
    }

    function save($data, $id) {
        parent::save($data, $id);
        $this->cache->context_delete($this->_model()->CACHE_KEY);
    }

}
