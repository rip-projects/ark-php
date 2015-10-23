<?php


/**
 * MY_Profiler.php
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


class MY_Profiler extends CI_Profiler {

	function _compile_session_data() {
		if ( ! isset($this->CI->session)) {
			return;
		}
		$output = '';
		$output .= '<h4>'.$this->CI->lang->line('profiler_session_data').'</h4>';
		$output .= "<div class=\"grid-container table-bordered\"><table id='ci_profiler_session_data' class=\"grid table table-hover table-striped table-condensed\">";

		foreach ($this->CI->session->all_userdata() as $key => $val) {
			$output .= "<tr><td>".$key."</td><td>".highlight_code(print_r($val,1))."</td></tr>\n";
		}
		$output .= '</table></div>';
		return $output;
	}

	protected function _compile_benchmarks() {
		$profile = array();
		foreach ($this->CI->benchmark->marker as $key => $val) {
			// We match the "end" marker so that the list ends
			// up in the order that it was defined
			if (preg_match("/(.+?)_end/i", $key, $match)) {
				if (isset($this->CI->benchmark->marker[$match[1].'_end']) AND isset($this->CI->benchmark->marker[$match[1].'_start'])) {
					$profile[$match[1]] = $this->CI->benchmark->elapsed_time($match[1].'_start', $key);
				}
			}
		}

		// Build a table containing the profile data.
		// Note: At some point we should turn this into a template that can
		// be modified.  We also might want to make this data available to be logged

		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_benchmarks').'</h4>';
		$output .= "\n";
		$output .= "\n\n<div class=\"grid-container table-bordered\"><table class=\"grid table table-hover table-striped table-condensed\">\n";

		foreach ($profile as $key => $val) {
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output .= "<tr><td>".$key."</td><td>".$val."</td></tr>\n";
		}

		$output .= "</table></div>\n";

		return $output;
	}

	/**
	 * Compile $_GET Data
	 *
	 * @return	string
	 */
	protected function _compile_get()
	{
		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_get_data').'</h4>';
		$output .= "\n";

		if (count($_GET) == 0)
		{
			$output .= "<div>".$this->CI->lang->line('profiler_no_get')."</div>";
		}
		else
		{
			$output .= "\n\n<div class=\"grid-container table-bordered\"><table class=\"grid table table-hover table-striped table-condensed\">\n";

			foreach ($_GET as $key => $val)
			{
				if ( ! is_numeric($key))
				{
					$key = "'".$key."'";
				}

				$output .= "<tr><td>&#36;_GET[".$key."]</td><td>";
				$output .= highlight_code(stripslashes(print_r($val, true)));
				$output .= "</td></tr>\n";
			}

			$output .= "</table></div>\n";
		}

		return $output;
	}

	/**
	 * Compile memory usage
	 *
	 * Display total used memory
	 *
	 * @return	string
	 */
	protected function _compile_memory_usage()
	{
		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_memory_usage').'</h4>';
		$output .= "\n";

		if (function_exists('memory_get_usage') && ($usage = memory_get_usage()) != '')
		{
			$output .= "<div>".number_format($usage).' bytes</div>';
		}
		else
		{
			$output .= "<div>".$this->CI->lang->line('profiler_no_memory')."</div>";
		}

		return $output;
	}

	/**
	 * Compile $_POST Data
	 *
	 * @return	string
	 */
	protected function _compile_post()
	{
		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_post_data').'</h4>';
		$output .= "\n";

		if (count($_POST) == 0)
		{
			$output .= "<div>".$this->CI->lang->line('profiler_no_post')."</div>";
		}
		else
		{
			$output .= "\n\n<div class=\"grid-container table-bordered\"><table class=\"grid table table-hover table-striped table-condensed\">\n";

			foreach ($_POST as $key => $val)
			{
				if ( ! is_numeric($key))
				{
					$key = "'".$key."'";
				}

				$output .= "<tr><td>&#36;_POST[".$key."]</td><td>";
				$output .= highlight_code(stripslashes(print_r($val, TRUE)));
				$output .= "</td></tr>\n";
			}

			$output .= "</table></div>\n";
		}

		return $output;
	}

	protected function _compile_uri_string()
	{
		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_uri_string').'</h4>';
		$output .= "\n";

		if ($this->CI->uri->uri_string == '')
		{
			$output .= "<div>".$this->CI->lang->line('profiler_no_uri')."</div>";
		}
		else
		{
			$output .= "<div>".$this->CI->uri->uri_string."</div>";
		}

		return $output;
	}

	/**
	 * Show the controller and function that were called
	 *
	 * @return	string
	 */
	protected function _compile_controller_info()
	{
		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_controller_info').'</h4>';
		$output .= "\n";

		$output .= "<div>".$this->CI->router->fetch_class()."/".$this->CI->router->fetch_method()."</div>";

		return $output;
	}

	protected function _compile_queries()
	{
		$dbs = array();

		// Let's determine which databases are currently connected to
		foreach (get_object_vars($this->CI) as $CI_object)
		{
			if (is_object($CI_object) && is_subclass_of(get_class($CI_object), 'CI_DB') )
			{
				$dbs[] = $CI_object;
			}
		}

		if (count($dbs) == 0)
		{
			$output  = "\n\n";
			$output .= "\n";
			$output .= '<h4>'.$this->CI->lang->line('profiler_queries').'</h4>';
			$output .= "\n";
			$output .= "\n\n<div>".$this->CI->lang->line('profiler_no_db')."</div>\n";

			return $output;
		}

		// Load the text helper so we can highlight the SQL
		$this->CI->load->helper('text');

		// Key words we want bolded
		$highlight = array('SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'OR&nbsp;', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', '(', ')');

		$output  = "\n\n";

		$count = 0;

		foreach ($dbs as $db)
		{
			$count++;

			$output .= "\n";
			$output .= '<h4>'.$this->CI->lang->line('profiler_database').': '.$db->database.' '.$this->CI->lang->line('profiler_queries').': '.count($db->queries).'</h4>';
			$output .= "\n";

			if (count($db->queries) == 0)
			{
				$output .= "<div>".$this->CI->lang->line('profiler_no_queries')."</div>\n";
			}
			else
			{
				$output .= "\n\n<div class=\"grid-container table-bordered\"><table id='ci_profiler_queries_db_{$count}' class=\"grid table table-hover table-striped table-condensed\">\n";
				foreach ($db->queries as $key => $val)
				{
					$time = number_format($db->query_times[$key], 4);

					$val = preg_replace('/[ \t]+/', ' ', $val);
					$val = highlight_code($val, ENT_QUOTES);

					foreach ($highlight as $bold)
					{
						$val = str_replace($bold, '<strong>'.$bold.'</strong>', $val);
					}

					$output .= "<tr><td>".$time."</td><td>".$val."</td></tr>\n";
				}
				$output .= "</table></div>\n";
			}

		}

		return $output;
	}

	/**
	 * Compile header information
	 *
	 * Lists HTTP headers
	 *
	 * @return	string
	 */
	protected function _compile_http_headers()
	{
		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_headers').'</h4>';
		$output .= "\n";

		$output .= "\n\n<div class=\"grid-container table-bordered\"><table id='ci_profiler_httpheaders_table' class=\"grid table table-hover table-striped table-condensed\">\n";

		foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD',' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR') as $header)
		{
			$val = (isset($_SERVER[$header])) ? $_SERVER[$header] : '';
			$output .= "<tr><td>".$header."</td><td>".$val."</td></tr>\n";
		}

		$output .= "</table></div>\n";

		return $output;
	}

	protected function _compile_config()
	{
		$output  = "\n\n";
		$output .= "\n";
		$output .= '<h4>'.$this->CI->lang->line('profiler_config').'</h4>';
		$output .= "\n";

		$output .= "\n\n<div class=\"grid-container table-bordered\"><table id='ci_profiler_config_table' class=\"grid table table-hover table-striped table-condensed\">\n";

		foreach ($this->CI->config->config as $config=>$val)
		{
			if (is_array($val))
			{
				$val = print_r($val, TRUE);
			}

			$output .= "<tr><td>".$config."</td><td>".highlight_code($val)."</td></tr>\n";
		}
		$output .= "</table></div>\n";
		return $output;
	}

}