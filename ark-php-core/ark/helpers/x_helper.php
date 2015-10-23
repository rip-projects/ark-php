<?php

/**
 * x_helper.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
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

if (!function_exists('xlog')) {

    function xlog($data, $to_screen = true) {
        if ($to_screen) {
            echo '<pre class="-debug prettyprint">';
            print_r($data);
            echo '</pre>' . "\n";
        }

        log_message('info', print_r($data, true));
    }

}

if (!function_exists('theme_url')) {

    function theme_url($uri = '') {
        static $theme_url;

        if (!$GLOBALS['CFG']->item('theme')) {
            return '';
        }

        if (!isset($theme_url)) {
            $CI = & get_instance();
            $CI->load->helper('url');
            $theme_url = base_url() . THEMEPATH . $GLOBALS['CFG']->item('theme') . '/';
        }

        if (strpos($uri, '://') === FALSE) {
            return $theme_url . $uri;
        }
        return $uri;
    }

}

if (!function_exists('data_url')) {

    function data_url($uri = '', $preset = 'thumb') {
        static $data_url;
        if (!isset($data_url)) {
            $CI = & get_instance();
            $CI->load->helper('url');
            $data_url = base_url() . 'data/';
        }

        if ($preset == '') {
            return $data_url . $uri;
        }

        if (strpos($uri, '://') === FALSE) {
            $pos = strrpos($uri, '/');
            $dir = substr($uri, 0, $pos).'/';
            $file = substr($uri, $pos);

            $uri = $data_url . $dir.$preset.$file;
        }
        return $uri;
    }

}

if (!function_exists('xview_filter')) {

    function xview_filter($filter, $extra = '') {
        $CI = &get_instance();
        return $CI->load->view('helpers/xview_filter', array(
            'filter' => $filter,
            'extra' => $extra,
                ), true);
    }

}

if (!function_exists('xview_error')) {

    function xview_error() {
        $err_string = get_error_string();
        if (!empty($err_string)) {
            return '<div class="error">' . $err_string . '</div>';
        }
    }

    function get_error_string() {
        $CI = &get_instance();
        $errors = get_errors();

        if (!empty($errors)) {
            $error_string = '';
            foreach ($errors as $error) {
                $error_string .= '<p>'.$error.(substr($error, -1) == '.' ? '' : '.').'</p>';
            }
            return $error_string;
        }
    }

    function get_errors($clean = true) {
        global $errors;
        $CI = &get_instance();
        if (empty($errors)) {
            $errors = $CI->session->userdata('validation::errors');
        }

        if ($clean) {
            $CI->session->set_userdata('validation::errors', NULL);
        }

        return $errors;
    }

    function is_error_exists() {
        global $errors;
        $CI = &get_instance();
        if (empty($errors) && isset($CI->session)) {
            $errors = $CI->session->userdata('validation::errors');
        }
        return !empty($errors);
    }

    function add_error($error) {
        global $errors;

        $error = (is_array($error)) ? $error : array($error);

        $CI = &get_instance();
        if (empty($errors)) {
            $errors = $CI->session->userdata('validation::errors');
            if (empty($errors)) {
                $errors = array();
            }
        }

        foreach($error as $e) {
            $errors[] = $e;
        }

        $CI->session->set_userdata('validation::errors', $errors);
    }

}

if (!function_exists('xview_info')) {

    function xview_info() {
        $err_string = get_info_string();
        if (!empty($err_string)) {
            return '<div class="info">' . $err_string . '</div>';
        }
    }

    function get_info_string() {
        $CI = &get_instance();
        $infos = get_infos();

        if (!empty($infos)) {
            $info_string = '';
            foreach ($infos as $info) {
                $info_string .= '<p>'.$info.(substr($info, -1) == '.' ? '' : '.').'</p>';
            }
            return $info_string;
        }
    }

    function get_infos($clean = true) {
        global $infos;
        $CI = &get_instance();
        if (empty($infos)) {
            $infos = $CI->session->userdata('validation::infos');
        }

        if ($clean) {
            $CI->session->set_userdata('validation::infos', NULL);
        }

        return $infos;
    }

    function is_info_exists() {
        global $infos;
        $CI = &get_instance();
        if (empty($infos)) {
            $infos = $CI->session->userdata('validation::infos');
        }
        return !empty($infos);
    }

    function add_info($info) {
        global $infos;

        $info = (is_array($info)) ? $info : array($info);

        $CI = &get_instance();
        if (empty($infos)) {
            $infos = $CI->session->userdata('validation::infos');
            if (empty($infos)) {
                $infos = array();
            }
        }

        foreach($info as $e) {
            $infos[] = $e;
        }

        $CI->session->set_userdata('validation::infos', $infos);
    }

}

if (!function_exists('date_parse_from_format')) {

    function date_parse_from_format($format, $date) {
        if ($format == 'Y-m-d H:i:s') {
            if (!empty($date)) {
                $date = explode(' ', $date);
                if (!empty($date[0])) {
                    $date[0] = explode('-', $date[0]);
                    if (!empty($date[1])) {
                        $date[1] = explode(':', $date[1]);
                    }
                }
            }
            return array(
                'year' => intval((empty($date[0][0])), '0', $date[0][0]),
                'month' => intval((empty($date[0][1])), '0', $date[0][1]),
                'day' => intval((empty($date[0][2])), '0', $date[0][2]),
                'hour' => intval((empty($date[1][0])), '0', $date[1][0]),
                'minute' => intval((empty($date[1][1])), '0', $date[1][1]),
                'second' => intval((empty($date[1][2])), '0', $date[1][2]),
            );
        }
        $dMask = array(
            'H' => 'hour',
            'i' => 'minute',
            's' => 'second',
            'y' => 'year',
            'm' => 'month',
            'd' => 'day'
        );
        $format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY);
        $date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($date as $k => $v) {
            if ($dMask[$format[$k]])
                $dt[$dMask[$format[$k]]] .= $v;
        }
        return $dt;
    }

}

if (!function_exists('l')) {

    function _l($key) {
        $CI = &get_instance();
        $CI->lang->load('messages');
        $val = (isset($CI->lang->language[$key])) ? $CI->lang->language[$key] : '';
        if (empty($val)) {
            $val = $key;
        }
        return $val;
    }

    function l($key, $params = array()) {
        $CI = &get_instance();
        if (is_string($params)) {
            $params = array($params);
        } elseif (empty($params)) {
            $params = array();
        }

        $params = array_merge(array(_l($key)), $params);
        if (count($params) == 1) {
            return $params[0];
        } else {
            return call_user_func_array('sprintf', $params);
        }
    }

}

if (!function_exists('get_image_path')) {

    function get_image_path($img, $type = 'thumb', $def = 'img/arch/no_image.jpg', $basedir = './data/', $baseurl = 'data/') {
        if (empty($img)) {
            return theme_url($def);
        }

        $baseurl = base_url() . $baseurl;

        $d = explode('/', $img);
        $a = array();
        $count = count($d);
        for ($i = 0; $i < $count - 1; $i++) {
            $a[] = $d[$i];
        }
        $a[] = $type;
        $a[] = $d[$count - 1];

        $uri = implode('/', $a);
        if (file_exists($basedir . $uri)) {
            return $baseurl . $uri;
        } else {
            return theme_url($def);
        }
    }

}

if (!function_exists('fork')) {

    function fork($shellCmd) {
        exec("nice $shellCmd > /dev/null 2>&1 &");
    }

}

if (!function_exists('my_timespan')) {

    function my_timespan($seconds = 1, $time = '') {
        $CI = & get_instance();
        $CI->lang->load('date');
        if (!is_numeric($seconds)) {
            $seconds = 1;
        }
        if (!is_numeric($time)) {
            $time = time();
        }
        if ($time <= $seconds) {
            $seconds = 1;
        } else {
            $seconds = $time - $seconds;
        }
        $str = '';
        $hours = floor($seconds / 3600);
        if ($hours > 0) {
            $str .= number_format($hours) . ' ' . $CI->lang->line((($hours > 1) ? 'date_hours' : 'date_hour')) . ', ';
        }
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        if ($hours > 0 OR $minutes > 0) {
            if ($minutes > 0) {
                $str .= $minutes . ' ' . $CI->lang->line((($minutes > 1) ? 'date_minutes' : 'date_minute')) . ', ';
            }
            $seconds -= $minutes * 60;
        }
        if ($str == '') {
            $str .= $seconds . ' ' . $CI->lang->line((($seconds > 1) ? 'date_seconds' : 'date_second')) . ', ';
        }
        return substr(trim($str), 0, -1);
    }

}

if (!function_exists('close_tags')) {

	function close_tags($string) {
		$open_tag = strrpos($string, '<');
		$close_tag = strrpos($string, '>');

		if ($close_tag < $open_tag) {
		    $string = substr($string, 0, $open_tag) . '<p>&#8230;</p>';
		}

	// coded by Constantin Gross <connum at googlemail dot com> / 3rd of June, 2006
	// (Tiny little change by Sarre a.k.a. Thijsvdv)
		$donotclose = array('br', 'img', 'input'); //Tags that are not to be closed
	//prepare vars and arrays
		$tagstoclose = '';
		$tags = array();

	//put all opened tags into an array  /<(([A-Z]|[a-z]).*)(( )|(>))/isU
		preg_match_all("/<(([A-Z]|[a-z]).*)(( )|(>))/isU", $string, $result);
		$openedtags = $result[1];
	// Next line escaped by Sarre, otherwise the order will be wrong
	// $openedtags=array_reverse($openedtags);
	//put all closed tags into an array
		preg_match_all("/<\/(([A-Z]|[a-z]).*)(( )|(>))/isU", $string, $result2);
		$closedtags = $result2[1];

	//look up which tags still have to be closed and put them in an array
		for ($i = 0; $i < count($openedtags); $i++) {
		    if (in_array($openedtags[$i], $closedtags)) {
		        unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		    }
		    else
		        array_push($tags, $openedtags[$i]);
		}

		$tags = array_reverse($tags); //now this reversion is done again for a better order of close-tags
	//prepare the close-tags for output
		for ($x = 0; $x < count($tags); $x++) {
		    $add = strtolower(trim($tags[$x]));
		    if (!in_array($add, $donotclose))
		        $tagstoclose.='</' . $add . '>';
		}

	//and finally
		return $string . $tagstoclose;
	}

}

if (!function_exists('notify_admin')) {

	function notify_admin($template, $data) {
		$CI = &get_instance();
		$users = $CI->db->query('SELECT * FROM user LEFT JOIN user_role ON user.id = user_role.user_id WHERE role_id = 1')->result_array();
		$emails = array();
		foreach ($users as $user) {
		    $emails[] = $user['email'];
		}

		$CI->load->library('xmailer');
		$CI->xmailer->send($template, $data, $emails);
	}




}

if (!function_exists('mysql_to_human')) {

    function mysql_to_human($d) {
        if (substr($d, 0, 10) == '0000-00-00') {
            return '';
        }
        $parsed = date_parse_from_format(l('format.mysql_datetime'), $d);
        $unix = mktime($parsed['hour'], $parsed['minute'], $parsed['second'], $parsed['month'], $parsed['day'], $parsed['year']);
        return date(l('format.short_date'), $unix);
    }

}

if (!function_exists('db_conn')) {
    function &db_conn($name) {
        static $nodb = null;

        $CI =& get_instance();

        if ($CI->config->item('use_db')) {
            if (!isset($CI->_db[$name])) {
                $CI->_db[$name] = $CI->load->database($name, TRUE);
                $CI->_db[$name]->trans_start();

                if ($CI->_db[$name]->dbdriver == 'oci8' || ($CI->_db[$name]->dbdriver == 'pdo' && substr($this->_db[$name]->hostname, 0, 3) == 'oci')) {
                    $CI->_db[$name]->query('ALTER SESSION SET NLS_DATE_FORMAT="YYYY-MM-DD HH24:MI:SS"');
                    $CI->_db[$name]->query('ALTER SESSION SET NLS_TIMESTAMP_FORMAT="YYYY-MM-DD HH24:MI:SS"');
                    $CI->_db[$name]->query('ALTER SESSION SET NLS_COMP=ANSI');
                    $CI->_db[$name]->query('ALTER SESSION SET NLS_SORT=BINARY_CI');
                }
                if ($name == 'default') $CI->db = &$CI->_db[$name];
            }
            return $CI->_db[$name];
        } else {
            return $nodb;
        }
    }
}