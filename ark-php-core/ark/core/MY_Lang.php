<?php

/**
 * MY_Lang.php
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
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

/**
 * Description of MY_Lang
 *
 * @author jafar
 */

class MY_Lang extends CI_Lang {
    function __construct() {
        $this->_defaults = array(
            'id' => 'id',
            'en' => 'us',
        );

        parent::__construct();
    }

    function fetch_language() {
        static $language;

        if ($language) {
            return $language;
        }

        if (!empty($_COOKIE['lang'])) {
            $language = $_COOKIE['lang'];
            if (is_dir(APPPATH.'language/'.$language)) {
                return $language;
            }
        }

        $config_files = array(
            APPPATH . 'config/' . ENVIRONMENT . '/config.php',
            ARCHPATH . 'config/app.php',
        );

        if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/app.php')) {
            $config_files[] = APPPATH . 'config/' . ENVIRONMENT . '/app.php';
        } elseif (file_exists(APPPATH . 'config/app.php')) {
            $config_files[] = APPPATH . 'config/app.php';
        }

        $tmp = array();
        foreach($config_files as $config_file) {
            if (file_exists($config_file)) {
                $config = array();
                include $config_file;
                $tmp = array_merge($tmp, $config);
            }
        }
        $config = $tmp;
        $default_lang = (empty($config['language'])) ? 'en_us' : $config['language'];

        if (isset($config['lang_force']) && $config['lang_force']) {
            $language = $default_lang;
        } else {
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $accept_lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach ($accept_lang as $data) {
                    $t1 = explode(';', $data);
                    $t1 = (strpos($t1[0], '-') !== FALSE) ? explode('-', $t1[0]) : explode('_', $t1[0]);
                    $t1[0] = strtolower($t1[0]);
                    $t1[1] = (count($t1) !== 2 && !empty($this->_defaults[$t1[0]])) ? $this->_defaults[$t1[0]] : strtolower($t1[1]);
                    $data = implode('_', $t1);
                    if (is_dir(APPPATH.'language/'.$data)) {
                        $language = $data;
                        break;
                    }
                }
            }

            if (empty($language)) {
                $language = $default_lang;
            }
        }
        $this->set_language($language);
        return $language;
    }

    function set_language($language) {
        global $CFG;

        if (is_dir(APPPATH.'language/'.$language)) {
            setcookie('lang', $language, time() + 86400, $CFG->config['cookie_path'], $CFG->config['cookie_domain']);
        }
    }

    function load_gettext($language = null, $domain = 'messages') {
        require_once ARCHPATH . '/helpers/x_helper.php';

        $language = $this->fetch_language();

        // get current timestamp
        $current_domain = '';
        if (empty($current_domain)) {
            $l = explode('.', $language);
            $domains = glob(APPPATH . 'language/locale/' . strtolower($l[0]) . '/LC_MESSAGES/messages-*.mo');
            if (!empty($domains)) {
                $current = basename($domains[0], '.mo');
                $timestamp = preg_replace('{messages-}i', '', $current);
                $current_domain = $current;
            }
        }

        if (empty($current_domain)) {
            $current_domain = $domain;
        }
        $language = explode('_', $language);
        $lang = $language[0].'_'.strtoupper($language[1]).'.UTF-8';
        putenv("LC_ALL=" . $lang);
        setlocale(LC_ALL, $lang);
        bindtextdomain($current_domain, APPPATH . 'language/locale');
        textdomain($current_domain);
    }

    function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
    {
        $langfile = str_replace('.php', '', $langfile);

        if ($add_suffix == TRUE)
        {
            $langfile = str_replace('_lang.', '', $langfile).'_lang';
        }

        $langfile .= '.php';

        if (in_array($langfile, $this->is_loaded, TRUE))
        {
            return;
        }

        // $config =& get_config();
        // reekoheek: using fetch_language() instead of from config
        if ($idiom == '') {
            $idiom = $deft_lang = $this->fetch_language();
        }

        // Determine where the language file is and load it
        if ($alt_path != '' && file_exists($alt_path.'language/'.$idiom.'/'.$langfile))
        {
            include($alt_path.'language/'.$idiom.'/'.$langfile);
        }
        else
        {
            $found = FALSE;
            if (file_exists(ARCHPATH.'language/'.$idiom.'/'.$langfile)) {
                include(ARCHPATH.'language/'.$idiom.'/'.$langfile);
                $found = TRUE;
            }
            foreach (get_instance()->load->get_package_paths(TRUE) as $package_path)
            {
                if (file_exists($package_path.'language/'.$idiom.'/'.$langfile))
                {
                    include($package_path.'language/'.$idiom.'/'.$langfile);
                    $found = TRUE;
                    break;
                }
            }

            if ($found !== TRUE)
            {
                show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
            }
        }


        if ( ! isset($lang))
        {
            log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
            return;
        }

        if ($return == TRUE)
        {
            return $lang;
        }

        $this->is_loaded[] = $langfile;
        $this->language = array_merge($this->language, $lang);
        unset($lang);

        log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
        return TRUE;
    }
}