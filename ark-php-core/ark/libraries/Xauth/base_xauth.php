<?php


/**
 * base_xauth.php
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

abstract class base_xauth extends CI_Driver {
	function __construct() {
		$ci = &get_instance();
		$ci->load->config('xauth', true);
		$config = $ci->config->item('xauth');

		$class = str_replace('xauth_', '', get_class($this));

		foreach($config as $key => $value) {
			if (isset($this->$key) && strpos($key, $class) === 0) {
				$this->$key = $value;
			}
		}
	}
	abstract function try_login($login, $password);
	abstract function try_logout();
	abstract function login_page($continue = '');
	abstract function privilege($uri, $user_id = '');
	abstract function is_login();
}