<?php

/**
 * Xauth.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2012/09/17 10:08:35
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2012/09/17 10:08:35   jafar <jafar@xinix.co.id>
 *
 *
 */

class xauth extends CI_Driver_Library {

    protected $valid_drivers    = array(
        'xauth_db', 'xauth_facebook', 'xauth_twitter', 'xauth_ldap', 'xauth_central', 'xauth_oauth', //'xauth_dummy'
    );

    var $_user;

    var $session_id = 'xauth.user';
    var $schema = array('db');

    var $ci;

    function __construct($params = array()) {
        if (!empty($params['schema']) && is_string($params['schema'])) {
            $params['schema'] = array($params['schema']);
        }

        if (empty($params['schema'])) {
            unset($params['schema']);
        }
        $this->ci = &get_instance();
        $this->initialize($params);
    }

    function _adapter() {
        if (empty($user['login_mode'])) {
            $user = $this->get_user();
            $mode = $user['login_mode'];
            $driver = 'xauth_'.$mode;
            if (in_array($driver, array_map('strtolower', $this->valid_drivers))) {
                return $this->{$mode};
            }
        }
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

    function set_user($user, $persistent = true) {
        $user['is_login'] = true;

        $this->_user = $user;

        if ($persistent) {
            $this->ci->session->set_userdata($this->session_id, $user);
        }
        return $user;
    }

    function get_user($refetch = false) {

        if ($refetch) {
            $user = $this->ci->_model('user')->refetch_user($user);
            $user['is_login'] = true;

            $this->_user = $user;
            $this->ci->session->set_userdata($this->session_id, $user);
        } elseif (empty($this->_user)) {
            $this->_user = $this->ci->session->userdata($this->session_id);
        }
        return $this->_user;
    }

    function get_user_object() {
        static $user;
        if (!$user) {
            $user = (object) $this->get_user();
        }
        return $user;
    }

    function add_user($user) {
        unset($user['id']);
        $id = $this->ci->_model('user')->save($user);
        if (!empty($id)) {
            $this->ci->_model('user')->add_user_role($id, 2);
        } else {
            redirect('site/error?msg='.rawurlencode('Cannot add new user, possibly duplicate entry.'));
        }
    }

    function login($login, $password, $mode = '') {
        if ($mode == 'db' || (empty($mode) && (empty($login) || empty($password)))) {
            return false;
        }
        if (empty($mode)) {
            $modes = $this->schema;
        } else {
            $modes = array($mode);
        }

        foreach ($modes as $mode) {
            if (in_array($mode, $this->schema)) {
                $driver = $this->{$mode};
                $user = $driver->try_login($login, $password);

                if (isset($user['id'])) {
                    $this->set_user($user);
                    return true;
                }
            }
        }
        return false;
    }

    function logout() {
        $adapter = $this->_adapter();
        if (!empty($adapter)) {
            $adapter->try_logout();
        }
        $this->_user = null;
        $this->ci->session->unset_userdata($this->session_id);
        $this->ci->session->sess_destroy();
        $this->ci->session->sess_create();
    }

    function is_login() {
        $adapter = $this->_adapter();
        if (!empty($adapter) && $adapter->is_login()) {
            $user = $this->get_user();
            if (!empty($user)) {
                return true;
            }
        }
        return false;
    }

    function privilege($uri, $user_id = '') {

        $adapter = $this->_adapter();

        if (!empty($adapter)) {
            $privilege = $adapter->privilege($uri, $user_id);
            return ($privilege >= 0) ? $privilege : $this->db->privilege($uri, $user_id);
        } else {
            $adapter = null;
            foreach($this->schema as $mode) {
                $_adapter = $this->{$mode};
                if ($_adapter->is_login()) {
                    $adapter = $_adapter;
                    break;
                }
            }
            if (!empty($adapter)) {
                $privilege = $adapter->privilege($uri, $user_id);
                return ($privilege >= 0) ? $privilege : $this->db->privilege($uri, $user_id);
            }
        }
        return 0;
    }

    function login_page($continue = '') {
        $adapter = $this->_adapter();
        if (!empty($adapter)) {
            $login_page = $adapter->login_page($continue);
        } else {
            foreach($this->schema as $mode) {
                $adapter = $this->{$mode};
                $login_page = $adapter->login_page($continue);
                if (!empty($login_page)) {
                    break;
                }
            }
        }
        if (empty($login_page)) {
            if (empty($continue)) {
                $continue = (isset($_GET['continue'])) ? $_GET['continue'] : current_url();
            }
            $login_page = site_url('user/login').'?continue='.$continue;
        }
        return $login_page;
    }

    function change_password_page($continue = '') {
        $adapter = $this->_adapter();

        if (!empty($adapter) && method_exists($adapter, 'change_password_page')) {
            $change_password_page = $adapter->change_password_page($continue);
        } else {
            foreach($this->schema as $mode) {
                $adapter = $this->{$mode};
                if (method_exists($adapter, 'change_password_page')) {
                    $change_password_page = $adapter->change_password_page($continue);
                    if (!empty($change_password_page)) {
                        break;
                    }
                }
            }
        }

        if (empty($change_password_page)) {
            if (empty($continue)) {
                $continue = (isset($_GET['continue'])) ? $_GET['continue'] : current_url();
            }
            $change_password_page = site_url('user/change_password').'?continue='.$continue;
        }
        return $change_password_page;
    }

}
