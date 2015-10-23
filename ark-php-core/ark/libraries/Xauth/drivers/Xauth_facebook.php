<?php


/**
 * Xauth_facebook.php
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
class xauth_facebook extends base_xauth {
    var $facebook_download_picture = false;

    function api_user() {
        $user_sso = $this->ci->facebook->user();
        if (isset($user_sso) && isset($user_sso->__resp) && !empty($user_sso->__resp->data)) {
            $user_sso = @$user_sso->__resp->data;
        } else {
            return array();
        }

        $user = array(
            'sso_facebook' => $user_sso->id,
            'username' => 'twitter_'.$user_sso->id,
        );

        if ($this->facebook_download_picture) {
            $pic_types = array('small', 'normal', 'large', 'square');
            foreach ($pic_types as $pic_type) {
                @mkdir('data/user/image/' . $pic_type, 0777, true);
                fork('wget "http://graph.facebook.com/' . $user_sso->id . '/picture?type=' . $pic_type . '" -O "./data/user/image/' . $pic_type . '/fb_' . $user_sso->id . '"');
            }
            $user['image'] = 'user/image/fb_' . $user_sso->id;
        } else {
            $user['image'] = 'http://graph.facebook.com/' . $user_sso->id . '/picture';
        }

        $user['email'] = $user_sso->email;
        $user['first_name'] = $user_sso->first_name;
        $user['last_name'] = $user_sso->last_name;
        $user['address'] = $user_sso->location->name;
        $user['locale'] = $user_sso->locale;
        $user['sso_verified'] = $user_sso->verified;

        $genders = array('male' => 1, 'female' => 2);
        $user['gender'] = (empty($user_sso->gender)) ? 0 : $genders[$user_sso->gender];

        if (!empty($user_sso->timezone)) {
            $this->ci->load->helper('date');
            $timezones = timezones();
            if ($user_sso->timezone > 0) {
                $t = '+'.$user_sso->timezone;
            } else {
                $t = '-'.abs($user_sso->timezone);
            }
            $timezone = 'UTC';
            foreach($timezones as $key => $val) {
                if ($t == $val) {
                    $timezone = $key;
                    break;
                }
            }
            $user['timezone'] = $timezone;
        } else {
            $user['timezone'] = 'UTC';
        }

        if (!empty($user_sso->birthday)) {
            $b = $user_sso->birthday;
            $e = explode('/', $b);
            if (count($e) == 3) {
                $user['dob'] = $e[2] . '-' . $e[0] . '-' . $e[1];
            } else {
                $user['dob'] = '';
            }
        }

        $db_user = $this->ci->_model('user')->get_login(array('sso_facebook' => $user['sso_facebook']));
        $user['row_status'] = (empty($db_user)) ? 'new' : 'existing';

        return $user;
    }

    function try_login($login, $password) {
        $this->ci->load->library('facebook');
        if (!$this->ci->facebook->logged_in()) {
            $this->ci->session->unset_userdata('continue');
            $continue = $this->ci->_get_redirect();
            $this->ci->session->set_userdata('continue', $continue);
            $this->ci->facebook->login();
            exit;
        } else {
            $remote_user = $this->api_user();
            if (!empty($remote_user) && $remote_user['row_status'] !== 'existing') {
                $this->add_user($remote_user);
                $this->ci->hooks->call_hook('lib:xauth:facebook:after_join');
            }
            $user = $this->ci->_model('user')->get_login(array('sso_facebook' => $remote_user['sso_facebook']));

            if (!empty($user)) {
                $user['login_mode'] = 'facebook';
                if (empty($user['status'])) {
                    redirect('user/denied');
                    exit;
                }
            }
            return $user;
        }
    }

    function try_logout() {
        $this->ci->load->library('facebook');
        if ($this->ci->facebook->logged_in()) {
            $this->ci->facebook->logout();
        }
    }

    function login_page($continue = '') {
        if (empty($continue)) {
            $continue = (isset($_GET['continue'])) ? $_GET['continue'] : base_url();
        }
        return site_url('user/login/facebook').'?continue='.$continue;
    }

    function privilege($uri, $user_id = '') {
        return -1;
    }

    function is_login() {
        return true;
    }

}