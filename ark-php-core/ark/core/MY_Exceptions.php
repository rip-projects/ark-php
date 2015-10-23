<?php

/**
 * MY_Exceptions.php
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

class MY_Exceptions extends CI_Exceptions {
    var $ci;

    function __construct() {
        parent::__construct();
        if (class_exists('CI_Controller')) {
            $this->ci = &get_instance();
        } else {
            require_once ARCHPATH . 'helpers/x_helper.php';
        }
    }

    function show_php_error($severity, $message, $filepath, $line)
    {
        $severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];

        $filepath = str_replace("\\", "/", $filepath);

        // For safety reasons we do not show the full file path
        if (FALSE !== strpos($filepath, '/'))
        {
            $x = explode('/', $filepath);
            $filepath = $x[count($x)-2].'/'.end($x);
        }

        if (ob_get_level() > $this->ob_level + 1)
        {
            ob_end_flush();
        }

        $prefix = (is_cli_request()) ? 'cli_' : '';

        ob_start();
        if (file_exists(APPPATH.'errors/' . $prefix . 'error_php.php')) {
            include(APPPATH.'errors/' . $prefix . 'error_php.php');
        } else {
            include(ARCHPATH.'errors/' . $prefix . 'error_php.php');
        }
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }

    /**
     * General Error Page
     *
     * This function takes an error message as input
     * (either as a string or an array) and displays
     * it using the specified template.
     *
     * @access  private
     * @param   string  the heading
     * @param   string  the message
     * @param   string  the template name
     * @param   int     the status code
     * @return  string
     */
    function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        if (is_cli_request()) {
            $prefix = 'cli_';
            $message = implode("\n", ( ! is_array($message)) ? array($message) : $message);
        } else {
            set_status_header($status_code);

            $prefix = '';
            $message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
        }

        if (ob_get_level() > $this->ob_level + 1)
        {
            ob_end_flush();
        }
        ob_start();
        if (file_exists(APPPATH.'errors/' . $prefix . $template .'.php')) {
            include(APPPATH.'errors/'.$prefix.$template.'.php');
        } else {
            include(ARCHPATH.'errors/'.$prefix.$template.'.php');
        }
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}

