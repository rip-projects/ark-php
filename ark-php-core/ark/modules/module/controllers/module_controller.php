<?php

/**
 * module.php
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

class module_controller extends app_base_controller {

    function __construct() {
        parent::__construct();
        $this->_validation = array(
            'add' => array(
                'name' => array('required|callback__check_module_name|callback__check_table_name'),
                'controller_base_class' => array('required'),
                'model_base_class' => array('required'),
            ),
        );
    }

    function index() {
        redirect('module/listing');
    }

    function _check_table_name($value) {
        if (!empty($_POST['create_table'])) {
            $row = $this->_model()->query("show tables like '$value'")->row_array();
            if (!empty($row)) {
                $this->form_validation->set_message('_check_table_name', 'Table is already exist.');
                return FALSE;
            }
        }
        return true;
    }

    function _check_module_name($value) {
        if (file_exists(APPMODPATH . $value)) {
            $this->form_validation->set_message('_check_module_name', 'Module is already exist.');
            return FALSE;
        }
        return true;
    }

    function listing() {
        // $this->_data['modules'] = $this->_model()->modules();
        // $this->_data['module_count'] = count($this->_data['modules']);
        // $count = 0;
        // foreach ($this->_data['modules'] as $module) {
        //     if (!empty($module['methods'])) {
        //         $count += count($module['methods']);
        //     }
        // }
        // $this->_data['method_count'] = $count;
    }

    function add() {
        if ($_POST) {
            if ($this->_validate()) {
                $base_path = APPMODPATH . $_POST['name'] . '/';
                mkdir($base_path);
                mkdir($base_path . 'controllers');
                mkdir($base_path . 'models');

                $data = array(
                    'module' => $_POST['name'],
                    'base_class' => $_POST['controller_base_class'],
                );


                $view = $this->load->view('templates/template_controller', $data, true);
                $f = fopen($base_path . 'controllers/' . $data['module'] . '.php', 'w');
                fputs($f, $view, strlen($view));
                fclose($f);

                $data = array(
                    'module' => $_POST['name'],
                    'base_class' => $_POST['model_base_class'],
                );

                $view = $this->load->view('templates/template_model', $data, true);
                $f = fopen($base_path . 'models/' . $data['module'] . '_model.php', 'w');
                fputs($f, $view, strlen($view));
                fclose($f);

                if (!empty($_POST['create_table'])) {
                    $sql = 'CREATE TABLE IF NOT EXISTS `' . $_POST['name'] . '` (
                        `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,' . "\n";
                    foreach ($_POST['fields'] as $key => $field) {
                        $type = strtoupper($_POST['types'][$key]);
                        $size = $_POST['sizes'][$key];
                        $extra = $_POST['extras'][$key];
                        if ($type == 'DATETIME' || $type == 'DATE' || $type == 'TEXT') {
                            $size = '';
                        }

                        if (empty($size)) {
                            if ($type == 'VARCHAR') {
                                $size = '(255)';
                            } else {
                                $size = '';
                            }
                        } else {
                            $size = '(' . $size . ')';
                        }

                        $sql .= '`' . $field . '` ' . $type . $size . ' ' . $extra . ' NOT NULL,' . "\n";
                    }
                    $sql .= '`status` INT(11) unsigned NOT NULL,
                        `created_time` DATETIME NOT NULL,
                        `created_by` VARCHAR(255) NOT NULL,
                        `updated_time` DATETIME NOT NULL,
                        `updated_by` VARCHAR(255) NOT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM' . "\n";

                    $this->_model()->query($sql);
                }
                redirect('module/listing');
            }
        } else {
            $_POST['controller_base_class'] = 'app_crud_controller';
            $_POST['model_base_class'] = 'app_base_model';
        }

        $this->_data['type_options'] = array(
            'INT' => 'INT',
            'VARCHAR' => 'VARCHAR',
            'TEXT' => 'TEXT',
            'DATETIME' => 'DATETIME',
            'DOUBLE' => 'DOUBLE',
        );
    }

}
