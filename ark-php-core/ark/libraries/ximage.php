<?php

/**
 * Ximage.php
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

require_once BASEPATH . 'libraries/Upload.php';
require_once ARCHPATH . 'libraries/MY_Upload.php';

class ximage extends MY_Upload {

    var $presets = array('thumb' => array('width' => 180, 'height' => 120));
    var $valid_required = true;
    var $data_dir = '';
    var $field = '';
    var $default_image = '';
    var $is_watermarked = false;

    function __construct($params = array()) {
        parent::__construct($params);
        $this->initialize($params);
    }

    function initialize($params = array()) {
        if (empty($params['allowed_types'])) {
            $params['allowed_types'] = 'gif|jpg|png';
        }

        if (empty($params['encrypt_name'])) {
            $params['encrypt_name'] = true;
        }

        $CI = &get_instance();

        if (empty($params['data_dir'])) {
            $params['data_dir'] = strtolower(get_class($CI)).'/'.$params['field'];
        }

        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }

        $upload_path = 'data/' . $this->data_dir;

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (is_string($this->allowed_types)) {
            $this->set_allowed_types($this->allowed_types);
        }

        parent:: initialize($params);

    }

    function get_image($data, $type = 'thumb', $data_dir = '', $default_image = '') {
        if (empty($data_dir)) {
            $data_dir = $this->data_dir;
        }
        if (empty($default_image)) {
            $default_image = $this->default_image;
        }
        $fields = explode('/', $data_dir);
        $field = $fields[count($fields) - 1];
        if (is_array($data)) {
            if (!empty($data[$field])) {
                $data = $data[$field];
            } else {
                $data = '';
            }
        } elseif (is_numeric($data)) {
            $CI = &get_instance();
            $data = $CI->_model($fields[0])->get($data);
            $data = $data[$field];
        }
        $uri = $data;
        if (empty($uri)) {
            $uri = $default_image;
        } else {
            $img = explode('/', $uri);
            $uri = $data_dir . '/' . $type . '/' . $img[count($img) - 1];
        }

        return $uri;
    }

    function do_upload($field = 'userfile') {
        $CI = &get_instance();

        $upload_path = 'data/' . $this->data_dir;
        $this->set_upload_path($upload_path);

        if (!parent::do_upload($field)) {
            return FALSE;
        }

        $uploaded = $this->data();

        $dirname = 'data/' . $this->data_dir . '/original/';
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }


        $_POST[$field] = array();
        foreach ($uploaded as $key => $value) {
            $_POST[$field][] = $this->data_dir . '/' . $value['file_name'];

            // copy original
            copy($value['full_path'], 'data/' . $this->data_dir . '/original/' . $value['file_name']);

            if ($this->is_watermarked) {
                $this->watermark($_POST);
            }
            foreach ($this->presets as $key => $preset) {
                $this->resize($_POST, $key);
            }

            unlink('data/'.$this->data_dir . '/' . $value['file_name']);
        }

        if (count($_POST[$field]) == 1) {
            $_POST[$field] = $_POST[$field][0];
        }

        return TRUE;
    }

    function resize($photo, $preset, $config = null) {
        if (is_string($preset)) {
            $tmp = $preset;
            $preset = $this->presets[$preset];
            $preset['name'] = $tmp;
        }
        $field = $this->field;

        $dirname = 'data/' . $this->data_dir . '/' . $preset['name'] . '/';
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }

        if (!empty($photo)) {
            foreach ($photo[$field] as $key => $value) {
                $f = explode('/', $value);
                $filename = $f[count($f) - 1];

                copy('data/' . $this->data_dir . '/original/' . $filename, $dirname . $filename);

                $CI = &get_instance();
                $config['source_image'] = $dirname . $filename;
                $config['width'] = $preset['width'];
                $config['height'] = $preset['height'];
                $CI->load->library('image_lib', $config);

                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
            }

        }
    }

    function watermark($photo, $config = null) {
        $field = $this->field;

        if (is_array($photo)) {
            $f = explode('/', $photo[$field]);
            $filename = $f[count($f) - 1];
        } elseif (is_object($photo)) {
            $f = explode('/', $photo->$field);
            $filename = $f[count($f) - 1];
        } else {
            throw new Exception('Error here and unhandled yet!');
        }

        if (!empty($photo)) {
            $dirname = 'data/' . $this->data_dir . '/' . $preset['name'] . '/';
            if (!file_exists($dirname)) {
                mkdir($dirname, 0777, true);
            }
            copy('data/' . $this->data_dir . '/original/' . $filename, $dirname . $filename);
            $CI = &get_instance();

            $CI->load->library('image_lib');
            if ($config === null) {
                $config['source_image'] = $dirname . $filename;
                $config['wm_type'] = 'overlay';
                $config['wm_vrt_alignment'] = 'bottom';
                $config['wm_hor_alignment'] = 'right';
                $config['wm_overlay_path'] = 'data/logo.png';
            }
            $CI->image_lib->initialize($config);
            $CI->image_lib->watermark();
        }
    }

}

