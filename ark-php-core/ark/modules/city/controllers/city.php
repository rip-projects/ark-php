<?php

/**
 * city.php
 *
 * @package     ark-php
 * @author      chalid <chalid@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   chalid <chalid@xinix.co.id>
 *
 *
 */

/**
 * Description of port
 *
 * @author chalid
 */
class city extends app_crud_controller {

     function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                'name' => array('required'),
                'province_id' => array('required', l('province')),
            ),
            'edit' => array(
                array(
                    'field' => 'name',
                    'label' => l('name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'province_id',
                    'label' => l('province'),
                    'rules' => 'required',
                ),
            ),
        );
    }

    function _get_name($value) {
        $result= $this->db->query("SELECT * FROM province WHERE id=?",$value)->row_array();
        return @$result['name'];
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('name', 'province_id');
        $config['names'] = array('Name', 'Province');
        $config['formats'] = array('row_detail', 'callback__get_name');
        return $config;
    }

    function _save($id = null) {
        parent::_save($id);

        $provinces = $this->_model()->query('SELECT * FROM province')->result_array();

        $this->_data['province_options']= array(''=> l('(Please select)'));
        foreach ($provinces as $pro) {
            $this->_data['province_options'][$pro['id']] = $pro['name'];
        }
    }

}
