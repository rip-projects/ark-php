<?php

/**
 * group.php
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

class group extends app_crud_controller {
    var $_validation = array(
        'add' => array(
            'name' => array('trim|required'),
        ),
        'edit' => array(
            'name' => array('trim|required'),
        ),
    );

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('name');
        return $config;
    }

    function _manage_organizations($id) {
        if (!empty($id)) {
            $this->_data['organizations'] = $this->_model()->get_organizations($id);
            $_orgs = array();
            foreach($this->_data['organizations'] as $org) {
                $_orgs[$org['org_id']] = $org['org_id'];
            }

            $config = array(
                'fields' => array('name'),
                'actions' => array(
                    'delete' => 'group/delete_organization/' . $id,
                ),
                'show_checkbox' => false,
            );
            $this->load->library('xgrid', $config, 'grid_organization');
        }

        $this->_data['org_options'] = array('' => l('(Please select)'));
        $orgs = $this->db->where('id !=', 1)->get('organization')->result_array();
        foreach ($orgs as $org) {
            if (empty($_orgs[$org['id']])) {
                $this->_data['org_options'][$org['id']] = $org['name'];
            }
        }
    }

    function add_organization($id) {
        if (!empty($_POST['org_id'])) {
            $this->_model()->add_organization($id, $_POST['org_id']);

            add_info(l('Group data added.'));
        } else {
            add_error(l('No data to add.'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    function delete_organization($id, $priv_id) {
        $this->_model()->delete_organization($id, $priv_id);

        add_info(l('Group data deleted.'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    function detail($id) {
        parent::detail($id);

        $this->_manage_organizations($id);

    }
}