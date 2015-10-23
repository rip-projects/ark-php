<?php

/**
 * MY_Security.php
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

/**
 * Description of MY_Security
 *
 * @author jafar
 */
class MY_Security extends CI_Security {

    var $_token;

    function create_token() {
        $CI = &get_instance();
        $CI->load->library('session');
        $this->_token = md5(uniqid(__METHOD__));
        $CI->session->set_userdata('token_hash', $this->_token);
    }

    function get_token() {
        $CI = &get_instance();
        $CI->load->library('session');
        if (!empty($this->_token)) {
            return $this->_token;
        } else {
            $this->_token = null;
            if (isset($CI->session)) {
                $this->_token = $CI->session->userdata('token_hash');
            }
            return $this->_token;
        }
    }

    function is_valid_token($token = '') {
        if (empty($token)) {
            if (!empty($_GET['csrf_token'])) {
                $token = $_GET['csrf_token'];
            } else if (!empty($_POST['csrf_token'])) {
                $token = $_POST['csrf_token'];
//            } else if (!empty($_COOKIE['csrf_token'])) {
//                $token = $_COOKIE['csrf_token'];
//            } else if (!empty($_REQUEST['csrf_token'])) {
//                $token = $_REQUEST['csrf_token'];
            }
        }

        $CI = &get_instance();
        $CI->load->library('session');
        $token_stored = $this->get_token();
        $CI->session->set_userdata('token_hash', null);

        return ($token === $token_stored);
    }

}