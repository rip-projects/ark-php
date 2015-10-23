<?php


/**
 * Xauth_central.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2012/09/11 22:03:05
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (yyyy/mm/dd hh:mm:ss) (author)
 * 2012/09/11 22:03:15   jafar <jafar@xinix.co.id>
 *
 *
 */

require_once (ARCHPATH.'/libraries/Xauth/base_xauth.php');

class xauth_central extends base_xauth {
    var $_token = '';
    var $_db;

    var $signin_uri = 'auth/signin';
    var $central_url = '';
    var $central_db = '';

    function __construct() {
        parent::__construct();

        $ci = &get_instance();
        if (empty($this->central_db)) {
            $this->_db = $ci->db;
        } else {
            $this->_db = db_conn($this->central_db);
        }
    }

    function try_login($login, $password) {
        if (isset($_GET['return_access_token'])) {
            $this->_set_token($_GET['return_access_token']);
        }
        if (!$this->is_login()) {
            $this->ci->session->unset_userdata('continue');
            $continue = $this->ci->_get_redirect();
            $this->ci->session->set_userdata('continue', $continue);
            $redirect_url = $this->central_url . $this->signin_uri . '?continue=' . rawurlencode(site_url('user/login/central') . '?js=r');
            redirect($redirect_url);
        } else {

            $remote_user = $this->api_user();
            if (!empty($remote_user) && $remote_user['row_status'] !== 'existing') {
                $this->add_user($remote_user);
            }

            $user = $this->ci->_model('user')->get_login(array('username' => $remote_user['username']));

            if (!empty($user)) {
                $user['login_mode'] = 'central';
                $user['access_token'] = $this->_token;
            }

            return $user;
        }
    }

    function is_login() {
        $token = $this->_get_token();
        if (!empty($token)) {
            return true;
        } elseif (!empty($_GET['checklogin'])) {
            redirect($this->login_page(current_url().'?continue='.$_GET['continue']));
        }
        return false;
    }

    function try_logout() {
        $token = $this->_get_token();
        $this->_db
            ->where('access_token', $token)
            ->delete('user_token');
        $this->ci->session->unset_userdata('xauth_central:token');
    }

    function privilege($uri, $user_id = '') {
        $remote_user = $this->api_user();
        if (empty($remote_user)) {
            $this->logout();
            return 0;
        } else {
            $user = $this->ci->_model('user')->get_login(array('username' => $remote_user['username']));
            if (!empty($user)) {
                $user['login_mode'] = 'central';
                $user['access_token'] = $this->_token;
                $this->set_user($user, false);
            }
        }
        return -1;
    }

    function api_user($access_token = '') {
        $access_token = (empty($access_token)) ? $this->_get_token() : $access_token;


        if (!empty($access_token)) {
            $sql = '
                SELECT u.* FROM user_token ut
                JOIN user u ON u.id = ut.user_id
                WHERE ut.access_token = ?
            ';
            $remote_user = $this->_db->query($sql, array($access_token))->row_array();
            if (!empty($remote_user)) {
                $user = $this->ci->_model('user')->get(array('username' => $remote_user['username']));

                if (empty($user)) {
                    $remote_user['row_status'] = 'new';
                } else {
                    $remote_user['row_status'] = 'existing';
                }
                return $remote_user;
            }
        }
    }

    function login_page($continue = '') {
        if (empty($continue)) {
            $continue = (isset($_GET['continue'])) ? $_GET['continue'] : base_url();
        }
        return site_url('user/login/central').'?continue='.rawurlencode($continue);
    }

    function change_password_page($continue = '') {
        if (empty($continue)) {
            $continue = (isset($_GET['continue'])) ? $_GET['continue'] : base_url();
        }
        return $this->central_url.'user/change_password?checklogin=1&continue='.$continue;
    }

    function _set_token($token) {
        $this->_token = $token;
        $this->ci->session->set_userdata('xauth_central:token',$this->_token);
    }

    function _get_token() {
        if (empty($this->_token)) {
            $this->_token = $this->ci->session->userdata('xauth_central:token');
        }
        return $this->_token;
    }
}