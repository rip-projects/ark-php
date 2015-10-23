<?php

/**
 * xform_helper.php
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
if (!function_exists('xform_autocomplete')) {

    function xform_autocomplete($name, $options = array(), $selected = '', $extra = '') {
        static $ID = 0;

        $CI = &get_instance();

        if (is_array($name)) {

        } else {
            $model = preg_replace('/_id$/', '', $name);
            $data = array(
                'name' => $name,
                'options' => site_url($model.'/rpc_get.json'),
            );
        }

        return $CI->load->view('helpers/xform_autocomplete', array(
                'ID' => $ID++,
                'data' => $data,
                'extra' => $extra,
                'selected' => $selected,
            ), true);
    }

}

if (!function_exists('xform_boolean')) {

    function xform_boolean($data, $default = '', $extra = '') {
        $options = array('No', 'Yes');
        return form_dropdown($data, $options, $default, $extra);
    }

}

if (!function_exists('xform_to_mdatetime')) {

    function xform_to_mdatetime($fdate, $ftime = '') {
        $fdate = explode('/', $fdate);
        $ftime = explode(':', $ftime);
        if (count($ftime) < 3) {
            $count = count($ftime);
            for ($i = $count; $i <= 3; $i++) {
                $ftime[] = '00';
            }
        }
        return trim($fdate[2]) . '-' . trim($fdate[1]) . '-' . trim($fdate[0]) . ' ' . trim($ftime[0]) . ':' . trim($ftime[1]) . ':' . trim($ftime[2]);
    }

}

if (!function_exists('xform_anchor')) {

    function xform_anchor($uri, $title, $attributes) {
        $CI = &get_instance();
        $uri = trim($uri, '/');
        if (method_exists($CI->_model('user'), 'privilege')) {
            if ($CI->_model('user')->privilege($uri)) {
                return anchor($uri, $title, $attributes);
            }
        }
        return false;
    }

}

if ( !function_exists('xform_lookup')) {

    function xform_lookup($field_name, $sgroup = '', $selected = array(), $extra = '') {
        $sgroup = (empty($sgroup)) ? $field_name : $sgroup;

        $CI = &get_instance();
        $rows = $CI->db->query('SELECT * FROM sysparam WHERE sgroup = ?', array($sgroup))->result_array();
        $options = array('' => l('(Please select)'));
        foreach($rows as $row) {
            $options[$row['skey']] = l($row['svalue']);
        }
        return form_dropdown($field_name, $options, $selected, $extra);
    }

}

if ( !function_exists('xform_llookup')) {

    function xform_llookup($field_name, $sgroup = '', $selected = array(), $extra = '') {
        $sgroup = (empty($sgroup)) ? $field_name : $sgroup;

        $CI = &get_instance();
        $rows = $CI->db->query('SELECT * FROM sysparam WHERE sgroup = ?', array($sgroup))->result_array();
        $options = array();
                $options[''] = '(Please select)';
        foreach($rows as $row) {
            $options[$row['skey']] = $row['lvalue'];
        }
        return form_dropdown($field_name, $options, $selected, $extra);
    }

}
if (!function_exists('xform_date')) {

    function xform_date($name, $options = array(), $extra = '') {
        static $first  = true;
        if (isset($_POST[$name])) {
            $_POST[$name] = (strpos($_POST[$name], '0000-00-00') !== FALSE) ? null: $_POST[$name];
        }

        $CI = &get_instance();
        $options['default_today'] = (isset($options['default_today'])) ? $options['default_today'] : true;
        $post = $_POST;
        if ($options['default_today'] && empty($post[$name])) {
            $post[$name] = date(l('format.mysql_datetime'));
        }

        $parsed = date_parse_from_format(l('format.mysql_datetime'), (empty($post[$name])) ? '' : $post[$name]);
        $unix = mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $parsed['month'], $parsed['day'], $parsed['year']);
        $post['humandate_' . $name] = date(l('format.short_date'), $unix);

        if (isset($options['show_time']) && $options['show_time']) {
            $post['humantime_' . $name] = date(l('format.short_time'), $unix);

            if (!empty($options['class'])) {
                preg_match('/span(\d+)/', $options['class'], $matches);
                if (!empty($matches)) {
                    $options['class'] = str_replace($matches[0], 'span'.(intval($matches[1]/2)), $options['class']);
                }
            }
        }

        $data = array(
            'name' => $name,
            'extra' => $extra,
            'options' => $options,
            'post' => $post,
            'include_first' => $first,
        );
        $first = false;
        return $CI->load->view('helpers/xform_date', $data, true);
    }
}

if (!function_exists('xform_model_lookup')) {

    function xform_model_lookup($field_name, $selected = array(), $extra = '') {
        $CI = &get_instance();

        $allow_empty = true;

        if (is_array($field_name)) {
            if (isset($field_name['allow_empty'])) {
                $allow_empty = $field_name['allow_empty'];
            }
            $model_name = $field_name['model'];
            $field_name = $field_name['field'];
        } else {
            $field_name = explode(':', $field_name);
            if (count($field_name) > 1) {
                $model_name = $field_name[1];
            } else {
                $model_name = preg_replace('/(.*)_id$/', '$1', $field_name);
            }
            $field_name = $field_name[0];
        }


        $options = ($allow_empty) ? array('' => l('(Please select)')) : array();
        $rows = $CI->db->get($model_name)->result_array();
        foreach ($rows as $row) {
            $options[$row['id']] = $row['name'];
        }
        return form_dropdown($field_name, $options, $selected, $extra);
    }

}

if (!function_exists('xform_multicheckbox')) {
    function xform_multicheckbox($field, $options, $selected, $extra) {
        $CI =& get_instance();

        if (is_array($field)) {
            $config = $field;
            $field = $field['field'];
        }

        $pos = strpos($field, '[');
        if ($pos !== FALSE) {
            $field = substr($field, 0, $pos);
        }
        return $CI->load->view('helpers/xform_multicheckbox', array(
            'field' => $field,
            'options' => $options,
            'selected' => $selected,
            'extra' => $extra,
            'config' => $config,
        ), true);
    }
}

if (!function_exists('xform_image')) {
    function xform_image($field) {
        return '<div><input type="file" name="'.$field.'" /><br/><img src="'.((!empty($_POST[$field])) ? get_image_path($_POST[$field]) : '').'" /></div>';
    }
}