<?php

/**
 * phpsession.php
 *
 * @package     ark-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

require_once (ARCHPATH.'/libraries/session/base_session.php');
class phpsession extends base_session {
	var $sess_cookie_name = 'xsession';
	var $sess_expiration = 3600;
	var $cookie_prefix = '';
	var $cookie_domain = '';
	var $cookie_path = '';
	var $cookie_secure = FALSE;

	function __construct() {
		$this->CI = &get_instance();
		$items = array('sess_cookie_name', 'sess_expiration', 'cookie_prefix', 'cookie_domain', 'cookie_path', 'cookie_secure');
		foreach ($items as $key) {
			$item = $this->CI->config->item($key);
			if (!empty($item)) {
				$this->$key = $item;
			}
		}

		if (!$this->CI->input->is_cli_request()) {
			if (empty($this->cookie_path)) {
                $x = explode($_SERVER['HTTP_HOST'], $this->CI->config->item('base_url'));
                $cookie_path = rtrim($x[1], '/');
                $this->cookie_path = ($cookie_path == '') ? '/' : $cookie_path;
                $this->CI->config->set_item('cookie_path', $this->cookie_path);
			}
		}

		parent::__construct();
	}

	function sess_init() {
		session_name($this->sess_cookie_name);
		session_set_cookie_params ($this->sess_expiration, $this->cookie_path, $this->cookie_domain);
		if (!is_cli_request()) {
			session_start();
		} else {
			$_SESSION = array();
		}

		if (isset($_COOKIE[$this->sess_cookie_name])) {
      	    setcookie($this->sess_cookie_name, $_COOKIE[$this->sess_cookie_name], time() + $this->sess_expiration, $this->cookie_path, $this->cookie_domain);
		}
	}

	function sess_read() {
		// Unserialize the session array
		$session = $_SESSION;

		if ( ! is_array($session) OR ! isset($session['session_id']) OR ! isset($session['ip_address']) OR ! isset($session['user_agent']) OR ! isset($session['last_activity'])) {
			$this->sess_destroy();
			return FALSE;
		}

		// Does the IP Match?
		if ($this->sess_match_ip == TRUE AND $session['ip_address'] != $this->CI->input->ip_address()) {
			$this->sess_destroy();
			return FALSE;
		}

		// Does the User Agent Match?
		if ($this->sess_match_useragent == TRUE AND trim($session['user_agent']) != trim(substr($this->CI->input->user_agent(), 0, 120))) {
			$this->sess_destroy();
			return FALSE;
		}

		// Session is valid!
		$this->userdata = $session;
		unset($session);

		return TRUE;
	}

	function sess_destroy() {
		if (!is_cli_request()) {
			session_destroy();
			session_start();
		}
		$_SESSION = array();
	}

	function sess_create() {
		$sessid = '';
		while (strlen($sessid) < 32) {
			$sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		$sessid .= $this->CI->input->ip_address();

		$this->userdata = array(
			'session_id'	=> md5(uniqid($sessid, TRUE)),
			'ip_address'	=> $this->CI->input->ip_address(),
			'user_agent'	=> substr($this->CI->input->user_agent(), 0, 120),
			'last_activity'	=> $this->now
			);

		$_SESSION = $this->userdata;
	}

	function sess_update() {
		// FIXME use it later but not now
	}

	function sess_write() {
		$_SESSION = $this->userdata;
	}

}