<?php

/**
 * user.php
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

class base_user_controller extends app_crud_controller {

    var $_trail_excluded = array('check_session'=>true);

    function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                'first_name' => array('trim|required'),
                'last_name'  => array('trim|required'),
                'email'      => array('trim|required|valid_email|callback__unique_email'),
                'username'   => array('trim|required|callback__unique_username'),
                'password'   => array('trim|required'),
                'password2'  => array('trim|required|callback__retypepassword_check', l('Retype Password')),
            ),
            'edit' => array(
                'first_name' => array('trim|required'),
                'last_name'  => array('trim|required'),
                'email'      => array('trim|required|valid_email|callback__unique_email'),
                'username'   => array('trim|required|callback__unique_username'),
                'password'   => array('trim|required'),
                'password2'  => array('trim|required|callback__retypepassword_check', l('Retype Password')),
            ),
            'change_password' => array(
                'password'     => array('trim|required'),
                'old_password' => array('trim|required|callback__old_password_check'),
                'password2'    => array('trim|required|callback__retypepassword_check', l('Retype Password')),
            ),
        );
    }

    function _retypepassword_check($value) {
        if ($value !== $_POST['password']) {
            $this->form_validation->set_message('_retypepassword_check', 'Password does not match.');
            return FALSE;
        }
        return true;
    }

    function _old_password_check($value) {
        $user = $this->auth->get_user();
        $id = $user['id'];
        $user = $this->_model()->get($id);
        $user_password = $user['password'];
        $old_password = md5($value);

        if ($user_password !== $old_password) {
            $this->form_validation->set_message('_old_password_check', 'Old Password does not match.');
            return FALSE;
        }
        return true;
    }

    function _unique_email($value, $row) {
        $suffix = (empty($this->uri->rsegments[3])) ? '' : ' AND id != ?';
        $user_id = (empty($this->uri->rsegments[3])) ? '' : $this->uri->rsegments[3];

        $count = $this->_model()->query('SELECT COUNT(*) count FROM user WHERE email LIKE ?'.$suffix, array($value, $user_id))->row()->count;
        if ($count == 0) {
            return true;
        } else {
            $this->form_validation->set_message('_unique_email', 'The %s field must be unique.');
            return FALSE;
        }
    }

    function _unique_username($value) {
        $suffix = (empty($this->uri->rsegments[3])) ? '' : ' AND id != ?';
        $user_id = (empty($this->uri->rsegments[3])) ? '' : $this->uri->rsegments[3];

        $count = $this->_model()->query('SELECT COUNT(*) count FROM user WHERE username LIKE ?'.$suffix, array($value, $user_id))->row()->count;
        if ($count == 0) {
            return true;
        } else {
            $this->form_validation->set_message('_unique_username', 'The %s field must be unique.');
            return FALSE;
        }
    }

    function login($mode = '') {
        $this->_layout_view = 'layouts/main';
        if ($_POST || !empty($mode)) {

            $is_login = $this->auth->login(($_POST) ? $_POST['login'] : '', ($_POST) ? $_POST['password'] : '', $mode);

            if ($is_login) {
                $this->_model('user')->add_trail('login');
                redirect($this->_get_redirect());
            } else {
                add_error(l('Username/email or password not found'));
            }
        } else {
            $this->_model('user')->add_trail('logout');
            $this->auth->logout();
        }
    }

    function logout() {
        $this->_model('user')->add_trail('logout');
        $this->auth->logout();
        redirect($this->_get_redirect());
    }

    function register() {

        // REMARK hanya yang memiliki code
        // if (empty($_GET['act'])) {
        //     show_404($this->uri->uri_string);
        // }
        // REMARK hanya yang memiliki code

        if ($_POST) {
            if ($this->_validate()) {
                $data = array(
                    'username'   => $_POST['username'],
                    'password'   => $_POST['password'],
                    'email'      => $_POST['email'],
                    'first_name' => $_POST['first_name'],
                    'last_name'  => $_POST['last_name'],
                );
                $this->_model()->register($data);

                $this->load->library('xmailer');
                $data = array(
                    'first_name' => $_POST['first_name'],
                    'last_name'  => $_POST['last_name'],
                    'username'   => $_POST['username'],
                );
                $this->xmailer->send('register', $data, $_POST['email']);

                $_POST['login'] = $_POST['username'];
                $this->login();
            }
        }
    }

    function profile($username = NULL) {
        $this->load->helper('format');

        $user = $this->auth->get_user();
        if (empty($username)) {
            if ($user['is_login']) {
                redirect('profile/' . $user['username']);
            } else {
                redirect('register');
            }
            exit;
        }


        $this->load->helper('gravatar');
        $user = $this->_model()->get(array('username' => $username));

        if (empty($user)) {
            show_404($this->uri->uri_string);
        }

        $sql = 'SELECT role_id id, name FROM ' . $this->db->dbprefix . 'user_role ug LEFT JOIN ' . $this->db->dbprefix . 'role g ON ug.role_id = g.id WHERE user_id = ?';
        $user['role'] = $this->db->query($sql, array($user['id']))->result_array();

        $this->_data['data'] = $user;

    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('username', 'first_name', 'last_name', 'email');
        $config['filters'] = array('username', 'name', 'email');
        return $config;
    }

    function _save($id = null) {
        $this->_view = $this->_name . '/show';

        $user_model = $this->_model('user');
        $user = $this->auth->get_user();

        if ($_POST) {
            if ($id && !$_POST['password']  && !$_POST['password2']) {
                unset($this->_validation['edit']['password']);
                unset($this->_validation['edit']['password2']);
            }

            if ($this->_validate()) {
                try {
                    $id = $user_model->save($_POST, $id);

                    add_info( ($id) ? l('Record updated') : l('Record added') );

                    if (!$this->input->is_ajax_request()) {
                        redirect($this->_get_uri('listing'));
                    }
                } catch (Exception $e) {
                    add_error(l($e->getMessage()));
                }

            }
        } else {
            if ($id !== null) {
                $this->_data['id'] = $id;
                $_POST = $user_model->get($id);
                $param = array($_POST['id']);

                $roles = $user_model->_db()->query('SELECT role_id FROM ' . $user_model->_db()->dbprefix . 'user_role WHERE user_id = ?', $param)->result_array();
                $_POST['roles'] = array();
                if (!empty($roles)) {
                    foreach ($roles as $role) {
                        $_POST['roles'][] = $role['role_id'];
                    }
                }
                $org = $user_model->_db()->query('SELECT org_id FROM ' . $user_model->_db()->dbprefix . 'user_organization WHERE user_id = ?', $param)->row_array();
                if (!empty($org)) {
                    $_POST['org_id'] = $org['org_id'];
                }

                $this->hooks->call_hook('user:fetch', $_POST);
            }
        }

        $_POST['password'] = '';

        if (!$user['is_top_member']) {
            $this->db->where('id !=', '1');
        }
        $roles =  $this->db->order_by('name')->get('role')->result_array();
        $this->_data['role_items'] = array('' => l('(Please select)'));
        foreach ($roles as $role) {
            $this->_data['role_items'][$role['id']] = $role['name'];
        }
        $orgs = $this->_model('organization')->find(null, array('name' => 'asc'));
        $this->_data['org_items'] = array('' => l('(Please select)'));
        foreach ($orgs as $org) {
            $this->_data['org_items'][$org['id']] = $org['name'];
        }
    }


    function _check_access() {
        $whitelist = array(
            'login',
            'unauthorized',
            'register',
            'check_session',
        );
        if (in_array($this->uri->rsegments[2], $whitelist)) {
            return true;
        }

        $whitelist = array(
            'change_password',
            'profile',
            'logout',
        );
        if (in_array($this->uri->rsegments[2], $whitelist) && $this->auth->is_login()) {
            return true;
        }

        return parent::_check_access();
    }

    function request_invitation() {
        $this->load->helper('captcha');
        $vals = array(
            'img_path'   => './captcha/',
            'img_url'    => base_url() . 'captcha/',
            'img_width'  => 150,
            'img_height' => 50,
        );

        $this->_data['cap'] = $cap = create_captcha($vals);

        $data = array(
            'captcha_time' => $cap['time'] . '',
            'ip_address'   => $this->input->ip_address(),
            'word'         => $cap['word']
        );

        $query = $this->db->insert_string('captcha', $data);
        $this->db->query($query);
    }

    function invite_friends() {

    }

    function edit_profile() {

    }

    function change_password() {

        $user = $this->auth->get_user();
        $id = $user['id'];

        if (empty($user) || empty($user['id'])) {
            redirect(base_url());
        }
        if ($_POST) {

            if ($this->_validate()) {
                unset($_POST['password2']);
                $data = array(
                    'password' => md5($_POST['password']),
                );
                $this->db->where('id', $id);
                $this->db->update('user', $data);
                redirect($this->_get_redirect());
            } else {
                add_error($this->form_validation->get_errors());
            }
        }
    }

    function inbox() {

    }

    function unauthorized() {

    }

    function check_session() {
        $user = $this->auth->get_user();
        $this->_data['data'] = $user['is_login'];
    }

    function detail($id) {
        parent::detail($id);

        $sql = 'SELECT role_id id, name FROM ' . $this->db->dbprefix . 'user_role ug LEFT JOIN ' . $this->db->dbprefix . 'role g ON ug.role_id = g.id WHERE user_id = ?';
        $this->_data['data']['role'] = $this->db->query($sql, array($id))->result_array();

        $this->load->helper('gravatar');
        $this->_view = 'user/profile';
    }
}

