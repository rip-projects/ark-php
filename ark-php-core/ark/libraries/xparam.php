<?php

/**
 * Xparam.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class Xparam {

    var $tables = array('sysparam');
    var $data;

    function __construct($params = array()) {
        $this->initialize($params);
    }

    function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

	function get($sgroup, $skey = NULL) {
		$CI = &get_instance();

		if (!empty($this->data[$sgroup])) {
			if ($skey == NULL) {
				return $this->data[$sgroup];
			}
			return (empty($this->data[$sgroup][$skey])) ? array() : $this->data[$sgroup][$skey];
		}

		$cache_key = __METHOD__.':'.$sgroup;
		$data = $CI->cache->context_get($cache_key);
		if ($data) {
			$this->data[$sgroup] = $data;

			if ($skey == '') {
				return $data;
			} else {
				return (empty($data[$skey])) ? array() : $data[$skey];
			}
		}

		foreach ($this->tables as $table) {
			$result = $CI->db->query('SELECT * FROM '.$table.' WHERE sgroup = ?', array($sgroup))->result_array();
			if (!empty($result)) {
				$data = array();
				foreach ($result as $row) {
					$data[$row['skey']] =  $row;
				}
				break;
			}
		}
		$this->data[$sgroup] = $data;
		$CI->cache->context_save($cache_key, $data, 60 * 60 * 24);

		if ($skey == NULL) {
			return $this->data[$sgroup];
		}
		return (empty($data[$skey])) ? array() : $data[$skey];
	}

}
