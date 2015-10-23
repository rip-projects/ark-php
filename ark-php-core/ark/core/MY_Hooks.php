<?php

/**
 * MY_Hooks.php
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

/**
 * undocumented class
 *
 * @package default
 * @author
 **/

class MY_Hooks extends CI_Hooks {

	function _initialize() {
		$CFG =& load_class('Config', 'core');

		// If hooks are not enabled in the config file
		// there is nothing else to do

		if ($CFG->item('enable_hooks') == FALSE) {
			return;
		}

		// Grab the "hooks" definition file.
		// If there are no hooks, we're done.

		if (defined('ENVIRONMENT') AND is_file(ARCHPATH.'config/'.ENVIRONMENT.'/hooks.php')) {
		    include(ARCHPATH.'config/'.ENVIRONMENT.'/hooks.php');
		} elseif (is_file(ARCHPATH.'config/hooks.php')) {
			include(ARCHPATH.'config/hooks.php');
		}

		if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/hooks.php')) {
		    include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
		} elseif (is_file(APPPATH.'config/hooks.php')) {
			include(APPPATH.'config/hooks.php');
		}

		if ( ! isset($hook) OR ! is_array($hook)) {
			return;
		}

		$this->hooks =& $hook;
		$this->enabled = TRUE;
	}

	function _run_hook($data, &$args = null) {
		if ( ! is_array($data)) {
			return FALSE;
		}

        if (empty($args)) {
            $params = array();
        } else {
            $params = array( &$args );
        }

		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------

		// If the script being called happens to have the same
		// hook call within it a loop can happen

		if ($this->in_progress == TRUE) {
			return;
		}

		// -----------------------------------
		// Set file path
		// -----------------------------------

		// if ( ! isset($data['filepath']) OR ! isset($data['filename'])) {
		// 	return FALSE;
		// }

		if (isset($data['filepath']) AND isset($data['filename'])) {
			$filepath = ARCHPATH.$data['filepath'].'/'.$data['filename'];
			if ( ! file_exists($filepath)) {
				$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];
				if ( ! file_exists($filepath)) {
					return FALSE;
				}
			}
		}

		// -----------------------------------
		// Set class/function name
		// -----------------------------------

		$class		= FALSE;
		$function	= FALSE;

        // params will be from function argument
		// $params		= array();

		if (isset($data['class']) AND $data['class'] != '') {
			$class = $data['class'];
		}

		if (isset($data['function'])) {
			$function = $data['function'];
		}

		if (isset($data['params'])) {
			$params = $data['params'];
		}

		if ($class === FALSE AND $function === FALSE) {
			return FALSE;
		}

		// -----------------------------------
		// Set the in_progress flag
		// -----------------------------------

		$this->in_progress = TRUE;

		// -----------------------------------
		// Call the requested class and/or function
		// -----------------------------------

		if ($class !== FALSE) {
			if ( !is_object($class) AND ! class_exists($class) AND isset($filepath)) {
				require($filepath);
			}
			call_user_func_array(array($class, $function), $params);
		} else {
			if (!is_object($function) AND ! function_exists($function) AND isset($filepath)) {
				require($filepath);
			}
			call_user_func_array($function, $params);
		}

		$this->in_progress = FALSE;
		return TRUE;
	}

	function call_hook($which = '', &$args = null) {
		$which = ':'.$which;

		if ( ! $this->enabled OR ! isset($this->hooks[$which])) {
			return FALSE;
		}

		foreach ($this->hooks[$which] as $val) {
            $this->_run_hook($val, $args);
		}

		return TRUE;
	}

	function add_hook($which, $function) {
		$which = ':'.$which;

		if (!is_array($function)) {
    		$hook = array(
    			'function' => $function,
    			'filepath' => 'hooks',
    		);
        } else {
            $hook = $function;
        }

		if (!isset($this->hooks[$which])) {
			$this->hooks[$which] = array();
		}
		$this->hooks[$which][] = $hook;
	}

	function remove_hook($which, $function = '') {
		$which = ':'.$which;

		if ($function === '') {
			unset($this->hooks[$which]);
			$this->hooks[$which] = array();
		} else {
			if (is_array($function)) {
				$class = $function[0];
				$function = $function[1];
			}

			$hooks = array();
			foreach($this->hooks[$which] as $hook) {
				if (isset($class) AND $class != '' && $hook['function'] == $function && $hook['class'] == $class) {
					continue;
				} elseif ($function == $hook['function']) {
					continue;
				}
				$hooks[] = $hook;
			}
			$this->hooks[$which] = $hooks;
		}

	}

    function load() {
        $CI =& get_instance();
        $base_paths = array(
            APPPATH.'hooks/',
            APPMODPATH.$CI->_name.'/hooks/',
            ARCHPATH.'hooks/',
            ARCHMODPATH.$CI->_name.'/hooks/',
        );

        foreach ($base_paths as $path) {
            if (file_exists($path.$CI->_name.'_hook.php')) {
                include($path.$CI->_name.'_hook.php');
                foreach ($hook as $k => $h) {
                    foreach($h as $eh) {
                        $this->add_hook($CI->_name.':'.$k, $eh);
                    }
                }
            }
        }
    }

}