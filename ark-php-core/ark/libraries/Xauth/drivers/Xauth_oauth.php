<?php


/**
 * Xauth_oauth.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2012/09/18 11:49:19
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (yyyy/mm/dd hh:mm:ss) (author)
 * 2012/09/18 11:49:19   jafar <jafar@xinix.co.id>
 *
 *
 */

require_once (ARCHPATH.'/libraries/Xauth/base_xauth.php');

class xauth_oauth extends base_xauth {

	function __construct() {
		parent::__construct();
		$ci = &get_instance();
		$ci->load->add_package_path(ARCHMODPATH . 'oauth');
		$ci->load->library('oauth_resource_server');
	}


	function try_login($login, $password) {

	}

	function is_login() {
		$user_id = $this->ci->oauth_resource_server->is_user();
		if (empty($user_id)) {
			return false;
		} else {
			$user = $this->ci->_model('user')->get_login(array('id' => $user_id));
			if (!empty($user)) {
				$user['login_mode'] = 'oauth';
	        	$this->set_user($user, false);
			}
			return true;
		}
	}

	function try_logout() {

	}

	function privilege($uri, $user_id = '') {
		$user = $this->get_user();
		if (!empty($user)) {
			$base_scope = preg_replace('/^(https?:\/\/)*(.*)$/', '$2', trim(base_url(),'/'));
			if ($this->ci->oauth_resource_server->has_scope(array($base_scope))) {
				return 1;
			}
		}
        return 0;
	}

	function login_page($continue = '') {

	}
}