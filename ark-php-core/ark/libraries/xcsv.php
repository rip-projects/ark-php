<?php

/**
 * xcsv.php
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

class xcsv {

    var $fields = array();
    var $filter = array();
    var $aligns = array();
    var $formats = array();
    var $names = array();
    var $filename = '';
    var $group_by = array();

    function __construct($params = array()) {
        $CI = &get_instance();
        $CI->load->helper('format_helper');

        $this->initialize($params);
    }

    function initialize($params = array()) {
        $CI = &get_instance();
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
        if (empty($this->filename)) {
            $this->filename = strtolower(get_class($CI));
        }
    }

    function generate_headers() {
        // header('Content-Type: text/plain');
        header('Content-Type: application/x-csv');
        header("Content-Disposition: inline; filename=\"" . $this->filename . ".csv\"");
    }

    function add_header() {
        $first = true;
        foreach($this->fields as $key => $field) {
            if ($first) {
                $first = false;
            } else {
                echo ',';
            }
            $name = (!empty($this->names[$key])) ? humanize($this->names[$key]) : humanize($field);
            echo '"'.str_replace('"', '\"', $name).'"';
        }
        echo "\n";
    }

    function add_row($row) {
        static $groups = array();
        $first = true;

        $reset = false;
        foreach($this->group_by as $group_by) {
            if (empty($groups[$group_by]) || $row[$group_by] != $groups[$group_by]) {
                $reset = true;
            }
            $groups[$group_by] = $row[$group_by];
        }

        if ($reset) {
            $gs = array();
            foreach($this->group_by as $group_by) {
                 $gs[] = '"'.$row[$group_by].'"';
            }
            echo implode(',', $gs)."\n";

            $this->add_header();
        }

        foreach($this->fields as $key => $field) {
            if ($first) {
                $first = false;
            } else {
                echo ',';
            }

            $cell = $row[$field];
            if (!empty($this->formats[$key])) {
                $format_method = 'format_'.$this->formats[$key];
                $cell = $format_method($cell);
            }

            echo '"'.str_replace('"', '\"', $cell).'"';
        }
        echo "\n";
    }

    function generate($data) {
        $CI = &get_instance();
        $this->generate_headers();
        foreach($data as $row)  {
            $this->add_row($row);
        }
    }

}

