<?php

/**
 * MY_Session.php
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

class MY_Session extends CI_Session {
		var $driver = '';
	var $is_custom_session = true;
	var $session_driver = null;

	function __construct($params = array()) {
		$this->CI =& get_instance();
		$this->driver = strtolower($this->CI->config->item('sess_driver'));

		if ($this->driver == 'database') {
			$this->CI->config->set_item('sess_use_database', true);
		}

		if ($this->driver == 'database' || $this->driver == 'cookie' || $this->driver == '') {
			$this->is_custom_session = false;
			parent::__construct($params);
		} else {
			$this->CI->load->library('session/'.$this->driver, $params, 'session_driver');
			$this->session_driver = &$this->CI->session_driver;

			// Delete 'old' flashdata (from last request)
			$this->_flashdata_sweep();

			// Mark all new flashdata as old (data will be deleted before next request)
			$this->_flashdata_mark();
		}
	}

	function sess_read() {
		if ($this->is_custom_session) {
			return $this->session_driver->sess_read();
		} else {
			return parent::sess_read();
		}
	}

	function sess_create() {
		if ($this->is_custom_session) {
			return $this->session_driver->sess_create();
		} else {
			return parent::sess_create();
		}
	}

	function sess_update() {
		if ($this->is_custom_session) {
			return $this->session_driver->sess_update();
		} else {
			return parent::sess_update();
		}
	}

	function sess_write() {
		if ($this->is_custom_session) {
			return $this->session_driver->sess_write();
		} else {
			return parent::sess_write();
		}
	}

	function sess_destroy() {
		if ($this->is_custom_session) {
			return $this->session_driver->sess_destroy();
		} else {
			return parent::sess_destroy();
		}
	}

	///////////////

	/**
	 * Fetch a specific item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function userdata($item) {
		if ($this->is_custom_session) {
			return $this->session_driver->userdata($item);
		} else {
			return parent::userdata($item);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	array
	 */
	function all_userdata() {
		if ($this->is_custom_session) {
			return $this->session_driver->all_userdata();
		} else {
			return parent::all_userdata();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_userdata($newdata = array(), $newval = '') {
		if ($this->is_custom_session) {
			return $this->session_driver->set_userdata($newdata, $newval);
		} else {
			return parent::set_userdata($newdata, $newval);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	function unset_userdata($newdata = array()) {
		if ($this->is_custom_session) {
			return $this->session_driver->unset_userdata($newdata);
		} else {
			return parent::unset_userdata($newdata);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Add or change flashdata, only available
	 * until the next request
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_flashdata($newdata = array(), $newval = '') {
		if ($this->is_custom_session) {
			return $this->session_driver->set_flashdata($newdata, $newval);
		} else {
			return parent::set_flashdata($newdata, $newval);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Keeps existing flashdata available to next request.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function keep_flashdata($key) {
		if ($this->is_custom_session) {
			return $this->session_driver->keep_flashdata($key);
		} else {
			return parent::keep_flashdata($key);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch a specific flashdata item from the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function flashdata($key) {
		if ($this->is_custom_session) {
			return $this->session_driver->flashdata($key);
		} else {
			return parent::flashdata($key);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Identifies flashdata as 'old' for removal
	 * when _flashdata_sweep() runs.
	 *
	 * @access	private
	 * @return	void
	 */
	function _flashdata_mark() {
		if ($this->is_custom_session) {
			return $this->session_driver->_flashdata_mark();
		} else {
			return parent::_flashdata_mark();
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Removes all flashdata marked as 'old'
	 *
	 * @access	private
	 * @return	void
	 */

	function _flashdata_sweep() {
		if ($this->is_custom_session) {
			return $this->session_driver->_flashdata_sweep();
		} else {
			return parent::_flashdata_sweep();
		}
	}
}