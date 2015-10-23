<?php


/**
 * Xauth_db.php
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

class Xauth_db extends base_xauth {
	function try_login($login, $password) {
        $user = $this->ci->_model('user')->get_login(array('login' => $login, 'password' => $password));
        if (!empty($user)) {
            $user['login_mode'] = 'db';
        }
        return $user;
    }

    function try_logout() {
    }

    function privilege($uri, $user_id = '') {
        $result = ($this->ci->_model('user')->privilege($uri->rsegments[1] . '/' . $uri->rsegments[2], $user_id) || $this->ci->_model('user')->privilege($uri->uri_string, $user_id));
        return ($result) ? 1 : 0;
    }

    function login_page($continue = '') {
    }

    function is_login() {
        return true;
    }
}