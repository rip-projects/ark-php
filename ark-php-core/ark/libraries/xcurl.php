<?php

/**
 * xcurl.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2012/09/04 12:52:44
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (yyyy/mm/dd hh:mm:ss) (author)
 * 2012/09/04 12:52:44   jafar <jafar@xinix.co.id>
 *
 *
 */


require_once ARCHPATH.'/libraries/Curl.php';
class xcurl extends Curl {

	var $response_headers = array();
	var $response_body = '';

	function _simple_call($method, $url, $params = array(), $options = array()) {
		$options[CURLOPT_HEADER] = true;
		$options[CURLOPT_FAILONERROR] = false;

        return parent::_simple_call($method, $url, $params, $options);
	}

	public function execute() {
		$result = parent::execute();
		if ($result === FALSE) {
			return FALSE;
		} else {
			$header = substr($result, 0, $this->info['header_size']);
			$body = substr($result, $this->info['header_size']);
			$header = explode("\n", $header);
			$headers = array();
			foreach($header as $h) {
				$h = trim($h);
				if (empty($h)) {
					continue;
				}
				$hd = explode(':', $h);
				if (count($hd) > 1) {
					$headers[trim($hd[0])] = trim($hd[1]);
				} else {
					$headers[trim($hd[0])] = trim($hd[0]);
				}
			}
			$this->response_headers = $headers;
			$this->response_body = $body;

			if ($this->info['http_code'] == 200) {
				return $this->response_body;
			} else {
				return FALSE;
			}
		}
	}

}