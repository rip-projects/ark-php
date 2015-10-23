<?php

/**
 * Xgrid.php
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

class Xgrid {

    var $show_checkbox = true;
    var $fields = array();
    var $filter = array();
    var $aligns = array();
    var $formats = array();
    var $names = array();
    var $sorts = array();
    var $actions = array();
    var $classes = array();
    var $dblclick_handler = '';
    var $custom_script = '';
    var $sort = '';
    var $context_menu;

    function __construct($params = array()) {
        $CI = &get_instance();
        $CI->load->helper('format_helper');

        $this->initialize($params);
    }

    function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    function highlight($row, $i, $filter_q) {
        $open_tag = '<span class="highlight-phrase">';
        $close_tag = '</span>';
        $str = (isset($row[$this->fields[$i]])) ? $row[$this->fields[$i]].'' : '';
        $highlighted = highlight_phrase($str, $filter_q, $open_tag, $close_tag);
        return $this->format($highlighted, $this->fields[$i], $row, $i);
    }

    function format($value, $field_name, $row, $index) {
        if (empty($this->formats[$index])) {
            return $value;
        }

        $formatter_method = $this->formats[$index];

        $matches = NULL;
        preg_match('/^([a-zA-Z_0-9]+)\((.*)\)$/', $formatter_method, $matches);
        if (empty($matches)) {
            $params = NULL;
        } else {
            $params = explode(',', $matches[2]);
            $formatter_method = $matches[1];
        }

        if (strpos($formatter_method, 'callback_') === 0) {
            $CI = &get_instance();
            $formatter_method = str_replace('callback_', '', $formatter_method);
            return $CI->$formatter_method($value, $field_name, $row, $index, $params);
        }

        $formatter_method = 'format_' . $formatter_method;
        if (function_exists($formatter_method)) {
            return $formatter_method($value, $field_name, $row, $index, $params);
        } else {
            return 'not supported formatter (' . $this->formats[$index] . ')';
        }
    }

    function show($data) {
        $CI = &get_instance();

        $libid = uniqid('lib_');
        $CI->load->library('xctxmenu', array(
            'actions' => $this->actions,
                ), $libid);
        $this->context_menu = $CI->$libid;

        return $CI->load->view('libraries/xgrid_show', array(
            'data' => $data,
            'self' => $this,
            'filter' => $this->filter,
                ), true);
    }

    function _sort_uri($field) {
        $s = 'asc';
        if (!empty($this->sort[$field])) {
            if (strtolower($this->sort[$field]) == 'asc') {
                $s = 'desc';
            }
        }
        return current_url() . '?sort=' . $field . ':' . $s;
    }

}

