<?php

/**
 * module_model.php
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

class module_model extends App_Base_Model {

    function _append_module($class_name, &$modules, $dir, $file) {
        if (!array_key_exists($class_name, $modules)) {
            require_once $dir . $file;
            $methods = get_class_methods($class_name);
            $module = array(
                'base_dir' => $dir,
                'name' => $class_name,
            );
            foreach ($methods as $method) {
                if (!preg_match('/^_|get_instance/', $method)) {
                    $module['methods'][] = $class_name . '/' . $method;
                }
            }
            $modules[$class_name] = $module;
        }
    }

    function modules() {
        $modules = array();

        $dir = APPPATH . 'controllers/';
        if (file_exists($dir)) {
            $dh = opendir($dir);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if (filetype($dir . $file) == 'file' && preg_match('/\.php$/', $file)) {
                        $exploded = explode('.', $file);
                        $class_name = $exploded[0];
                        $this->_append_module($class_name, $modules, $dir, $file);
                    }
                }
                closedir($dh);
            }
        }


        foreach(array(APPMODPATH, ARCHMODPATH) as $dir) {
            if (!file_exists($dir)) continue;
            $dh = opendir($dir);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if (filetype($dir . $file) == 'dir' && !preg_match('/^\./', $file)) {
                        $class_name = $file;
                        $dir1 = $dir . $file . '/controllers/';
                        if (!file_exists($dir1)) continue;
                        $d1 = opendir($dir1);
                        if ($d1) {
                            while (($file = readdir($d1)) !== false) {
                                if (filetype($dir1 . $file) == 'file' && preg_match('/' . $class_name . '\.php$/', $file)) {
                                    $this->_append_module($class_name, $modules, $dir1, $file);
                                }
                            }
                            closedir($d1);
                        }
                    }
                }
                closedir($dh);
            }
        }

        $dir = ARCHPATH . 'controllers/';
        $dh = opendir($dir);
        if ($dh) {
            while (($file = readdir($dh)) !== false) {
                if (filetype($dir . $file) == 'file' && preg_match('/\.php$/', $file) && !preg_match('/_controller\.php$/', $file)) {
                    $exploded = explode('.', $file);
                    $class_name = $exploded[0];
                    $this->_append_module($class_name, $modules, $dir, $file);
                }
            }
            closedir($dh);
        }

        return $modules;
    }

}
