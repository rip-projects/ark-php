<?php

/**
 * province.php
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

class province extends App_Crud_Controller {
     function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                array(
                    'field' => 'name',
                    'label' => l('name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'country_id',
                    'label' => l('country'),
                    'rules' => 'required',
                ),
            ),
            'edit' => array(
                array(
                    'field' => 'name',
                    'label' => l('name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'country_id',
                    'label' => l('country'),
                    'rules' => 'required',
                ),
            ),
        );
    }

    function _get_name($value) {
        $result= $this->db->query("SELECT * FROM country WHERE id=?",$value)->row_array();
        return @$result['name'];
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('name', 'country_id');
        $config['names'] = array('Name', 'Country');
        $config['formats'] = array('row_detail', 'callback__get_name');
        return $config;
    }

    function _save($id = null) {
        parent::_save($id);

        $countrys = $this->_model()->query('SELECT * FROM country')->result_array();

        $this->_data['country_options']= array(''=> l('(Please select)'));
        foreach ($countrys as $co) {
            $this->_data['country_options'][$co['id']] = $co['name'];
        }
    }
}
