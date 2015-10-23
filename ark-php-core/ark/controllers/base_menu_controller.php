<?php

/**
 * menu.php
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

class base_menu_controller extends app_crud_controller {

    function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                array(
                    'field' => 'title',
                    'label' => l('Title'),
                    'rules' => 'required',
                ),
            ),
            'edit' => array(
                array(
                    'field' => 'title',
                    'label' => l('Title'),
                    'rules' => 'required',
                ),
            ),
        );
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('title', 'uri');
        $config['names'] = array('', '');
        $config['sorts'] = array(0, 0);
        $config['formats'] = array('row_detail', '',);
        $config['filters'] = array('title', 'uri');
        $config['actions'] = array(
            'left' => 'menu/left',
            'right' => 'menu/right',
            'up' => 'menu/up',
            'down' => 'menu/down',
            'edit' => 'menu/edit',
            'delete' => 'menu/delete',
        );
        return $config;
    }

    function left($id) {
        $user = $this->auth->get_user();
        $menu = $this->_model()->query('SELECT * FROM menu WHERE id = ?', $id)->row_array();
        $parent = $this->_model()->query('SELECT * FROM menu WHERE id = ?', $menu['parent_id'])->row_array();
        $menu['parent_id'] = $parent['parent_id'];
        $this->_model()->save($menu, $menu['id']);
        $this->cache->context_delete($this->_model()->CACHE_KEY);
        redirect('menu/listing');
    }

    function right($id) {
        $user = $this->auth->get_user();

        $menu = $this->_model()->query('SELECT * FROM menu WHERE id = ?', $id)->row_array();
        $before = $this->_model()->query('
                SELECT * FROM menu WHERE parent_id = ? AND position <= ? ORDER BY position DESC, id DESC LIMIT 1,1
            ', array($menu['parent_id'], $menu['position']))->row_array();
        if (!empty($before)) {
            $this->_model()->save(array('parent_id' => $before['id']), $menu['id']);
        }
        $this->cache->context_delete($this->_model()->CACHE_KEY);
        redirect('menu/listing');
    }

    function up($id) {
        $user = $this->auth->get_user();

        $menu = $this->_model()->query('SELECT * FROM menu WHERE id = ?', $id)->row_array();
        $siblings = $this->_model()->query('SELECT * FROM menu WHERE parent_id = ? ORDER BY position, id', $menu['parent_id'])->result_array();

        unset($before_key);
        foreach ($siblings as $key => $sibling) {
            $siblings[$key]['new_position'] = $key;
            if ($sibling['id'] == $menu['id']) {

                if (isset($before_key)) {
                    $siblings[$key]['new_position'] = $siblings[$before_key]['new_position'];
                    $siblings[$before_key]['new_position'] = $key;
                }
            }
            $before_key = $key;
        }

        foreach ($siblings as $key => $sibling) {
            $this->_model()->save(array('position' => $sibling['new_position']), $sibling['id']);
        }
        $this->cache->context_delete($this->_model()->CACHE_KEY);
        redirect('menu/listing');
    }

    function down($id) {
        $user = $this->auth->get_user();

        $menu = $this->_model()->query('SELECT * FROM menu WHERE id = ?', $id)->row_array();
        $siblings = $this->_model()->query('SELECT * FROM menu WHERE parent_id = ? ORDER BY position, id', $menu['parent_id'])->result_array();

        $found = false;
        foreach ($siblings as $key => $sibling) {
            if ($found) {
                $found = false;
                continue;
            }
            $siblings[$key]['new_position'] = $key;
            if ($sibling['id'] == $menu['id']) {
                $found = true;
                if ($key + 1 < count($siblings)) {
                    $siblings[$key]['new_position'] = $key + 1;
                    $siblings[$key + 1]['new_position'] = $key;
                }
            }
        }

        foreach ($siblings as $key => $sibling) {
            $this->_model()->save(array('position' => $sibling['new_position']), $sibling['id']);
        }
        $this->cache->context_delete($this->_model()->CACHE_KEY);
        redirect('menu/listing');
    }

    function _save($id = null) {
        parent::_save($id);

        $menus = $this->_model()->query('SELECT * FROM menu')->result_array();
        $this->_data['parent_options'][0] = 'Top';
        foreach ($menus as $menu) {
            $this->_data['parent_options'][$menu['id']] = $menu['title'] . ' (' . $menu['uri'] . ')';
        }
    }

    function listing($offset = 0) {
        $this->load->library('pagination');

        $config_grid = $this->_config_grid();
        $config_grid['sort'] = $this->_get_sort();
        $config_grid['filter'] = $this->_get_filter();
        $per_page = $this->pagination->per_page;

        $count = 0;
        $this->_data['data']['items'] = $this->_model()->find_hierarchical($config_grid['filter'], $config_grid['sort'], $per_page, $offset, $count);
        $this->_data['filter'] = $config_grid['filter'];
        $this->load->library('xgrid', $config_grid, 'listing_grid');
        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'total_rows' => $count,
            'per_page' => $per_page,
        ));
    }

}
