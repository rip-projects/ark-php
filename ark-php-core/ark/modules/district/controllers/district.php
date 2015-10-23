<?php

/**
 * district.php
 *
 * @package     ark-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

class district extends app_crud_controller {

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
                    'field' => 'city_id',
                    'label' => l('city'),
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
                    'field' => 'city_id',
                    'label' => l('city'),
                    'rules' => 'required',
                ),
            ),
        );
    }

    function _get_name($value) {
        $result= $this->db->query("SELECT * FROM city WHERE id=?",$value)->row_array();
        return @$result['name'];
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('name', 'city_id');
        $config['names'] = array('Name', 'City');
        $config['formats'] = array('row_detail', 'callback__get_name');
        return $config;
    }

    function _save($id = null) {
        parent::_save($id);

        $citys = $this->_model()->query('SELECT * FROM city')->result_array();

        $this->_data['city_options']= array(''=> l('(Please select)'));
        foreach ($citys as $ci) {
            $this->_data['city_options'][$ci['id']] = $ci['name'];
        }
    }
}