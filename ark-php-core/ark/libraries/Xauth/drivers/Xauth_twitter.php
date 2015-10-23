<?php


/**
 * Xauth_twitter.php
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
 * (yyyy/mm/dd hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

require_once (ARCHPATH.'/libraries/Xauth/base_xauth.php');

class Xauth_twitter extends base_xauth {

	function try_login($login, $password) {
        $this->ci->load->library('tweet');
        if (!$this->ci->tweet->logged_in()) {
            $this->ci->session->unset_userdata('continue');
            $continue = $this->ci->_get_redirect();
            $this->ci->session->set_userdata('continue', $continue);
            $this->ci->tweet->login();
            exit;
        } else {
            $remote_user = $this->api_user();

            if (!empty($remote_user) && $remote_user['row_status'] !== 'existing') {
                $this->add_user($remote_user);
            }

            $user = $this->ci->_model('user')->get_login(array('sso_twitter' => $remote_user['sso_twitter']));

            if (!empty($user)) {
                $user['login_mode'] = 'twitter';

                if (empty($user['status'])) {
                    redirect('user/denied');
                    exit;
                }
            }

            return $user;
        }
	}

    function api_user() {
        $this->ci->load->library('tweet');

        $remote_user = $this->ci->tweet->call('get', 'account/verify_credentials');

        $user = array(
            'sso_twitter' => $remote_user->id,
            'username' => 'twitter_'.$remote_user->id,
        );

        $name = explode(' ', $remote_user->name);
        if (empty($name)) {
            $name = explode(' ', $remote_user->screen_name);
        }
        $user['first_name'] = $name[0];
        $user['last_name'] = $name[count($name) - 1];

        $this->ci->load->helper('date');
        $timezones = timezones();
        if ($remote_user->utc_offset > 0) {
            $t = '+'.($remote_user->utc_offset / 3600);
        } else {
            $t = '-'.abs($remote_user->utc_offset / 3600);
        }
        $timezone = 'UTC';
        foreach($timezones as $key => $val) {
            if ($t == $val) {
                $timezone = $key;
                break;
            }
        }
        $user['timezone'] = $timezone;

        $user['locale'] = $remote_user->lang;
        $user['sso_verified'] = $remote_user->verified;
        $user['image'] = $remote_user->profile_image_url;


        $db_user = $this->ci->_model('user')->get_login(array('sso_twitter' => $user['sso_twitter']));
        $user['row_status'] = (empty($db_user)) ? 'new' : 'existing';

        return $user;
    }

    function try_logout() {
        $this->ci->load->library('tweet');
        if ($this->ci->tweet->logged_in()) {
            $this->ci->tweet->logout();
        }
    }

    function login_page($continue = '') {
        if (empty($continue)) {
            $continue = (isset($_GET['continue'])) ? $_GET['continue'] : base_url();
        }
        return site_url('user/login/twitter').'?continue='.$continue;
    }

    function privilege($uri, $user_id = '') {
        return -1;
    }

    function is_login() {
        return true;
    }

}