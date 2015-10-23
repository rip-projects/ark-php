<?php

/**
 * format_helper.php
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

if (!function_exists('format_number')) {

    function format_number($value) {
        return number_format(doubleval($value));
    }

}

if (!function_exists('format_money')) {
    function format_money($value) {
        return number_format($value, 3);
    }

}


if (!function_exists('format_boolean')) {

    function format_boolean($value) {
        return ($value) ? 'Yes' : 'No';
    }

}
if (!function_exists('format_gender')) {

    function format_gender($value) {
        $genders = array('-', 'M', 'F');
        return $genders[$value];
    }

}

if (!function_exists('format_short_datetime')) {

    function format_short_datetime($value) {
        if (empty($value) || substr($value, 0, 10) == '0000-00-00') {
            return '';
        }
        return date(l('format.short_datetime'), strtotime($value));
    }

}

if (!function_exists('format_short_time')) {

    function format_short_time($value) {
        if (empty($value)) {
            return '';
        }
        return date(l('format.short_time'), strtotime($value));
    }

}

if (!function_exists('format_short_date')) {

    function format_short_date($value) {
        if (empty($value) || substr($value, 0, 10) == '0000-00-00') {
            return '';
        }
        return date(l('format.short_date'), strtotime($value));
    }

}

if (!function_exists('format_long_date')) {

    function format_long_date($value) {
        if (empty($value) || substr($value, 0, 10) == '0000-00-00') {
            return '';
        }
        return date(l('format.long_date'), strtotime($value));
    }

}

if (!function_exists('format_password')) {

    function format_password($value, $field_name) {
        return str_repeat('*', strlen($value));
    }

}

if (!function_exists('format_image')) {

    function format_image($value, $field_name, $a, $b, $params) {
        if (empty($value)) {
            return '';
        }
        if (!empty($params)) {
            $size = @getimagesize(data_url($value));
            if (!$size) {
                return '';
            }
            if ($size[0] > $size[1]) {
                $w = $params[0];
                $h = round(($params[0] * $size[1]) / $size[0]);
            } else {
                $h = $params[1];
                $w = round(($params[1] * $size[0]) / $size[1]);
            }
        }

        if (!empty($h) && !empty($w)) {
            return '<a href="' . data_url($value) . '" class="image-popup"><img class="preview_image" src="' . data_url($value) . '" width="' . $w . '" height="' . $h . '" /></a>';
        } else {
            return '<img class="preview_image" src="' . data_url($value) . '" />';
        }
    }

}

if (!function_exists('format_row_detail')) {

    function format_row_detail($value, $field, $row, $index, $params) {
        $CI = &get_instance();
        $segment = (!empty($params[0])) ? $params[0] : $CI->uri->rsegments[1];
        $id_field = (!empty($params[1])) ? $params[1] : 'id';

        return sprintf('<a href="%s">%s</a>', site_url($segment . '/detail/' . $row[$id_field]), $value);
    }

}

if (!function_exists('format_url')) {

    function format_url($value, $field, $row, $index) {
        $CI = &get_instance();
        $CI->load->helper('url');
        return anchor($value);
    }

}

if (!function_exists('format_param_short')) {

    function format_param_short($value, $field, $row = '', $index = '', $params = '') {
        $CI = &get_instance();
        if (!empty($params[0])) {
            $field = $params[0];
        }
        $param = $CI->xparam->get($field, $value);
        return (empty($param['svalue'])) ? '' : $param['svalue'];
    }

}

if (!function_exists('format_param_long')) {

    function format_param_long($value, $field) {
        $CI = &get_instance();
        $param = $CI->xparam->get($field, $value);
        return (empty($param['lvalue'])) ? '' : $param['lvalue'];
    }

}

if (!function_exists('format_plain_limit')) {

    function format_plain_limit($value, $field, $row, $index, $params = NULL) {
        $value = strip_tags($value);
        if (empty($params[0])) {
            $newval = character_limiter($value, 70);
        } else {
            $newval = character_limiter($value, $params[0]);
        }
        return '<span title="' . $value. '">' . $newval . '</span>';
    }

}

if (!function_exists('format_first_thumb')) {

    function format_first_thumb($value, $field = NULL, $row = NULL, $index = NULL, $params = NULL) {
        $rel = format_first_thumb_url($value, $field, $row, $index, $params);
        if (empty($rel)) {
            return '';
        }
        $size = @getimagesize(theme_url($rel));
        if (empty($size)) {
            return '';
        }
        if ($size[0] > $size[1]) {
            $w = ($params[0]) ? $params[0] : 100;
            $h = round(($w * $size[1]) / $size[0]);
        } else {
            $h = ($params[1]) ? $params[1] : 100;
            $w = round(($h * $size[0]) / $size[1]);
        }
        return '<a href="' . $rel . '" class="image-popup"><img class="preview_image" src="' . $rel . '" width="' . $w . '" height="' . $h . '" /></a>';
    }

}

if (!function_exists('format_first_thumb_url')) {

    function format_first_thumb_url($value, $field = NULL, $row = NULL, $index = NULL, $params = NULL) {
        preg_match('@<img.+src="(.*)".*>@Uims', $value, $matches);

        if (empty($matches[1])) {
            return '';
        }
        $purl = parse_url($matches[1]);
        if (isset($purl['scheme'])) {
            return $matches[1];
        } else {
            $rel = $purl['path'];
            $burl = parse_url(base_url());

            $pos = strpos($purl['path'], $burl['path']);
            if ($pos === 0) {
                $rel = substr($matches[1], strlen(base_url()));
            }
            return $rel;
        }
    }

}

if (!function_exists('format_youtube')) {

    function format_youtube($value) {
        $url = parse_url($value);
        parse_str($url['query'], $q);
        $video_id = $q['v'];
        if (empty($video_id)) {
            return '';
        }
        return '<a href="http://www.youtube.com/embed/' . $video_id . '?rel=0&amp;wmode=transparent" class="video-popup">
                <img class="preview_image" src="http://img.youtube.com/vi/' . $video_id . '/0.jpg" width="110" height="83" />
            </a>';
    }
}

if (!function_exists('format_model_param')) {

    function format_model_param($value, $field_name, $_1 = '', $_2 = '', $params = '') {
        static $map = array();

        $CI = &get_instance();
        if (!empty($params[0])) {
            $key = $params[0];
        } else {
            $key = 'name';
        }
        if (!empty($params[1])) {
            $table_name = $params[1];
        } else {
            $table_name = preg_replace('/(.+)_id$/', '$1', $field_name);
        }

        $cache_key = $table_name.':'.$value.':'.$key;

        if (!isset($map[$cache_key])) {
            $row = $CI->db->where('id', $value)->get($table_name)->row_array();
            if (empty($row)) {
                $result = '';
            } else {
                $keys = explode('+', $key);
                $result = array();
                foreach($keys as $key) {
                    $result[] = $row[$key];
                }
                $result = implode(' ', $result);
            }
            $map[$cache_key] = $result;
        } else {
            $result = $map[$cache_key];
        }

        return $result;
    }
}

if (!function_exists(('format_status_light'))) {
    function format_status_light($value) {
        return ($value) ? '<span class="active">OK</span>' : '<span class="inactive">NOT&nbsp;OK</span>';
    }
}

if (!function_exists('format_percentage')) {
    function format_percentage($value, $f, $_1, $_2, $params = '') {
        $multiplier = (empty($params[0])) ? 100 : $params[0];
        $divider = (empty($params[1])) ? 100 : $params[0];
        return (round($value * ($multiplier * $divider)) / $divider).'%';
    }
}