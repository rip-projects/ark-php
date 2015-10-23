<?php

if ( ! function_exists('xlog')) {

	function xlog($data, $to_screen = true) {
		$d = print_r($data, 1);
	    if ($to_screen) {
            if (!is_cli_request()) {
	           echo '<pre class="-debug prettyprint">';
            } else {
                echo '['.date('Y-m-d H:i:s')."] ";
            }
	        echo $d."\n\n";
            if (!is_cli_request()) {
    	        echo '</pre>' . "\n";
            }
	    }

	    if (function_exists('log_message')) {
	    	log_message('info', $d);
	    }
	}

}

if ( ! function_exists('show_exception'))
{
	function show_exception($exception)
	{
		header("Status: 500 Internal Server Error", TRUE);
		include(ARCHPATH.'errors/error_php.php');
	}
}

if ( ! function_exists('load_class')) {

	function &load_class($class, $directory = 'libraries', $prefix = 'CI_')
	{
		static $_classes = array();

		// Does the class exist?  If so, we're done...
		if (isset($_classes[$class]))
		{
			return $_classes[$class];
		}

		$name = FALSE;

		// Look for the class first in the local application/libraries folder
		// then in the native system/libraries folder
		foreach (array(APPPATH, ARCHPATH, BASEPATH) as $path)
		{
			if (file_exists($path.$directory.'/'.$class.'.php'))
			{
				$name = $prefix.$class;

				if (class_exists($name) === FALSE)
				{
					require($path.$directory.'/'.$class.'.php');
				}

				break;
			}
		}

		// Is the request a class extension?  If so we load it too
		if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
		{
			$name = config_item('subclass_prefix').$class;

			if (class_exists($name) === FALSE)
			{
				require(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php');
			}
		} elseif (file_exists(ARCHPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
		{
			$name = config_item('subclass_prefix').$class;


			if (class_exists($name) === FALSE)
			{
				require(ARCHPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php');
			}
		}

		// Did we find the class?
		if ($name === FALSE)
		{
			// Note: We use exit() rather then show_error() in order to avoid a
			// self-referencing loop with the Excptions class
			exit('Unable to locate the specified class: '.$class.'.php');
		}

		// Keep track of what we just loaded
		is_loaded($class);

		$_classes[$class] = new $name();
		return $_classes[$class];
	}

}

if ( ! function_exists('is_cli_request')) {

	function is_cli_request() {
		return (php_sapi_name() == 'cli') or defined('STDIN');
	}

}

if (!function_exists('trans_complete')) {
	function trans_complete() {
		$CI =& get_instance();
		if (isset($CI)) {
			$has_error = is_error_exists();
			if (!$has_error) {
	            if (!empty($CI->_db)) {
					foreach ($CI->_db as $db) {
		           	if (!$db->trans_status()) {
		            		$has_error = true;
		            		break;
		            	}
		            }
		            if ($has_error) throw new Exception('Unhandled database error. Please check your query');

		            foreach ($CI->_db as $db) {
		            	$db->trans_complete();
		            }
	            }
			}
		}
	}
}

if ( ! function_exists('redirect'))
{
	function redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		if ( ! preg_match('#^[a-zA-Z-_]+://#i', $uri))
		{
			$uri = site_url($uri);
		}

		trans_complete();

		switch($method)
		{
			case 'refresh'	: header("Refresh:0;url=".$uri);
				break;
			default			: header("Location: ".$uri, TRUE, $http_response_code);
				break;
		}
		exit;
	}
}
