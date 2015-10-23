<?php

/**
 * MY_Form_validation.php
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

class MY_Form_validation extends CI_Form_validation {
	public function set_rules($field, $label = '', $rules = '') {
		// No reason to set rules if we have no POST data
		if (count($_POST) == 0) {
			return $this;
		}

		if (is_array($field) && ($field!== array_values($field))) {
			foreach ($field as $key => $value) {
				$label = (!empty($value[1])) ? $value[1] : humanize($key);
				parent::set_rules($key, $label, $value[0]);
			}
			return $this;
		}
		parent::set_rules($field, $label, $rules);
	}

	function get_errors() {
		return $this->_error_array;
	}

	protected function _translate_fieldname($fieldname)
	{
		// Do we need to translate the field name?
		// We look for the prefix lang: to determine this
		if (substr($fieldname, 0, 5) == 'lang:')
		{
			// Grab the variable
			$line = substr($fieldname, 5);

			// Were we able to translate the field name?  If not we use $line
			if (FALSE === ($fieldname = $this->CI->lang->line($line)))
			{
				return '<b>'.l($line).'</b>';
			}
		}

		return '<b>'.l($fieldname).'</b>';
	}
}