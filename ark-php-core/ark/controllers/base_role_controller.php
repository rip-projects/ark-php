<?php

/**
 * role.php
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

class base_role_controller extends app_crud_controller {

    function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                'name' => array('trim|required'),
            ),
            'edit' => array(
                'name' => array('trim|required'),
            ),
        );
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('name', 'is_main');
        $config['formats'] = array('row_detail', 'boolean');
        return $config;
    }

    function _manage_privileges($id) {
        if (!empty($id)) {
            $this->_data['privileges'] = $this->_model()->get_privileges($id);
            $config = array(
                'fields' => array('uri'),
                'actions' => array(
                    'delete' => 'role/delete_privilege/' . $id,
                ),
                'show_checkbox' => false,
            );
            $this->load->library('xgrid', $config, 'grid_privilege');
        }
    }

    function _save($id = null) {
        parent::_save($id);

        $this->_manage_privileges($id);
    }

    function add_privilege($id) {
        if (!empty($_POST['uri'])) {
            $this->_model()->add_privilege($id, $_POST['uri']);
            $this->cache->context_delete($this->_model()->CACHE_KEY_PRIVILEGE);
            add_info(l('Privilege added.'));
        } else {
            add_error(l('No privilege to add.'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    function delete_privilege($id, $priv_id) {
        $this->_model()->delete_privilege($id, $priv_id);
        $this->cache->context_delete($this->_model()->CACHE_KEY_PRIVILEGE);

        add_info(l('Privilege deleted.'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    function detail($id) {
        $this->_data['data'] = $this->_model()->get($id);

        $this->_manage_privileges($id);
    }

}
