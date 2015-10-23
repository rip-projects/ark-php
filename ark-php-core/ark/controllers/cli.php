<?php

/**
 * cli.php
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

class cli extends CI_Controller {

    function __construct() {
        parent::__construct();
        include APPPATH . 'config/' . ENVIRONMENT . '/database.php';
        $this->db = $db;
        $this->load->helper(array('x', 'url'));
        $this->load->config('app');
    }

    function _rrmdir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                $this->_rrmdir($file);
            else
                unlink($file);
        }
        @rmdir($dir);
    }

    function lang() {
        if (!$this->input->is_cli_request()) {
            echo '<pre>';
        }

        $locale_dir = APPPATH . 'language/locale';
        $lang_dir = APPPATH . 'language';
        $output_dir = $locale_dir . '/default/LC_MESSAGES';
        $arch_output_dir = ARCHPATH . 'language/locale/default/LC_MESSAGES';
        $lock_file = './install/lang/xlang.lock';

        $now = time();
        $micro = microtime();
        if (!empty($micro)) {
            $micro = $micro * 100000000;
            $now = date('YmdHis', $now) . intval($micro);
        } else {
            $now = date('YmdHis', $now) . '00000000';
        }

        $which_find = exec('which find');
        if (strpos(strtolower($which_find), 'windows') !== FALSE) {
            $which_find = 'C:\cygwin\bin\find';
        }

        if (!file_exists($lock_file)) {
            @touch($lock_file);

			if (!file_exists($output_dir)) {
            	mkdir($output_dir, 0777, true);
			}
            if (file_exists($output_dir . '/messages.po')) {
                unlink($output_dir . '/messages.po');
            }
            touch($output_dir . '/messages.po');

			if (!file_exists($arch_output_dir)) {
            	mkdir($arch_output_dir, 0777, true);
			}
            if (file_exists($arch_output_dir . '/messages.po')) {
                unlink($arch_output_dir . '/messages.po');
            }
            touch($arch_output_dir . '/messages.po');

            $output = '';
            $cmd = $which_find . ' '.APPPATH.' -iname "*.php" -print0 | xargs -0 xgettext --language=PHP --indent --omit-header --no-location --sort-output --output-dir="' . $output_dir . '" --keyword=l -j -f -';
            exec($cmd, $output);

            $cmd = $which_find . ' '.ARCHPATH.' -iname "*.php" -print0 | xargs -0 xgettext --language=PHP --indent --omit-header --no-location --sort-output --output-dir="' . $arch_output_dir . '" --keyword=l -j -f -';
            exec($cmd, $output);

            $d = opendir($lang_dir);
            while ($entry = readdir($d)) {
                if (is_dir($lang_dir . '/' . $entry) && $entry[0] !== '.' && strtolower($entry) !== 'default'  && strtolower($entry) !== 'locale') {
                    echo "Create language for $entry\n";

                    $this->_rrmdir($locale_dir . '/' . $entry);
                    $lang_file = $lang_dir.'/'.$entry.'/messages_lang.php';
                    $lang = array();
                    include($lang_file);
                    $f = fopen($output_dir . '/messages.po', 'r');
                    $fw = fopen($lang_file, 'w');
                    fputs($fw, "<?php\n\n");
                    fputs($fw, "/** Generated code. Please dont add manually, it will be discarded on next generate cycle **/\n\n");
                    while($line = fgets($f)) {
                        if (substr($line, 0, 5) === 'msgid') {
                            $exploded = explode('"', $line, 2);
                            $key_line = substr($exploded[1], 0, (count($exploded[1])-3));
                            while($line = fgets($f)) {
                                if ($line[0] === ' ') {
                                    $exploded = explode('"', $line, 2);
                                    $key_line .= substr($exploded[1], 0, (count($exploded[1])-3));
                                } else {
                                    break;
                                }
                            }
                            eval('$key_key = "'.$key_line.'";');

                            fputs($fw, '$lang["'.$key_line."\"] = '".@stripslashes($lang[$key_key])."';\n");
                        }
                    }
                    fclose($f);
                    fclose($fw);

                    $lang_file = ARCHPATH.'language/'.$entry.'/messages_lang.php';
                    $lang = array();
                    @include($lang_file);
                    $f = fopen(ARCHPATH.'language/locale/default/LC_MESSAGES/messages.po', 'r');
                    $fw = fopen($lang_file, 'w');
                    fputs($fw, "<?php\n\n");
                    fputs($fw, "/** Generated code. Please dont add manually, it will be discarded on next generate cycle **/\n\n");
                    while($line = fgets($f)) {
                        if (substr($line, 0, 5) === 'msgid') {
                            $exploded = explode('"', $line, 2);
                            $key_line = substr($exploded[1], 0, (count($exploded[1])-3));
                            while($line = fgets($f)) {
                                if ($line[0] === ' ') {
                                    $exploded = explode('"', $line, 2);
                                    $key_line .= substr($exploded[1], 0, (count($exploded[1])-3));
                                } else {
                                    break;
                                }
                            }
                            eval('$key_key = "'.$key_line.'";');

                            fputs($fw, '$lang["'.$key_line."\"] = '".@stripslashes($lang[$key_key])."';\n");
                        }
                    }
                    fclose($f);
                    fclose($fw);
                }
            }
            closedir($d);
            @unlink($lock_file);
        } else {
            echo "install/lang is already running\n";
        }

        if (!$this->input->is_cli_request()) {
            echo '</pre>';
        }
    }

    function _clean_sql($dump_file) {
        $this->load->helper('date');
        // copy($dump_file, $dump_file . '.backup-working-' . date('YmdHis'));

        @unlink($dump_file . '.1');
        $f = fopen($dump_file, 'r');
        $f1 = fopen($dump_file . '.1', 'w');

        while ($line = fgets($f)) {
            if (substr($line, 0, 3) == '/*!') {
                continue;
            }
            $line = str_replace('),(', "),\n(", $line);
            $line = str_replace('VALUES (', "VALUES\n(", $line);
            $line = preg_replace('/ AUTO_INCREMENT=(\d+)/e', '', $line);
            $line = preg_replace('/\(\d+,/i', '(NULL,', $line);
            $line = preg_replace("/'\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}'/i", 'NOW()', $line);
            $line = preg_replace("/NOW\\(\\),'*\\w+'*/i", "NOW(),'0'", $line);

            if (strpos($line, "`created_by` varchar(255) NOT NULL,") !== FALSE) {
                $line = str_replace("`created_by` varchar(255) NOT NULL,", "`created_by` int(11) NOT NULL,", $line);
            }

            if (strpos($line, "`updated_by` varchar(255) NOT NULL,") !== FALSE) {
                $line = str_replace("`updated_by` varchar(255) NOT NULL,", "`updated_by` int(11) NOT NULL,", $line);
            }

            fputs($f1, $line, strlen($line));
        }

        fclose($f);
        fclose($f1);

        @unlink($dump_file);
        rename($dump_file . '.1', $dump_file);
    }

    function dump_db() {
        if (!$this->input->is_cli_request()) {
            show_404($this->uri->uri_string);
        }

        foreach ($this->db as $d) {
            if ($d['dbdriver'] == 'pdo') {
                $d['_hostname'] = $d['hostname'];

                $t = explode(':', $d['hostname']);
                $d['dbdriver'] = $t[0];

                $t = explode(';', $t[1]);
                $o = array();
                foreach($t as $s) {
                    $s = explode('=', $s);
                    $o[$s[0]] = $s[1];
                }
                $d['hostname'] = $o['host'];
            }
            $dump_file = APPPATH.'install/db/' . $d['database'] . '.sql';
            $output = '';
            $cmd = sprintf('mysqldump -h"%s" -u"%s" -p"%s" "%s" > "%s"', $d['hostname'], $d['username'], $d['password'], $d['database'], $dump_file);
            exec($cmd, $output);
            $to_file = explode('/application/', $dump_file, 2);
            $to_file = $to_file[1];
            $this->_print('Database "' . $d['database'] . '" dumped to '. $to_file);
            $this->_print($output);

            $this->_clean_sql($dump_file);
            $this->_print('Database SQL "' . $d['database'] . '" cleaned');
        }
    }

    function meld_db() {
        if (!$this->input->is_cli_request()) {
            show_404($this->uri->uri_string);
        }

        foreach($this->db as $d) {
            $output = '';
            $cmd = sprintf('meld "'.APPPATH.'/install/db/%s.sql" "'.APPPATH.'install/init/init.sql"', $d['database']);
            exec($cmd, $output);
            $this->_print($output);
            break;
        }
    }

    function init($force = false) {
        if (!$this->input->is_cli_request()) {
            show_404($this->uri->uri_string);
        }

        foreach ($this->db as $d) {

            if ($d['dbdriver'] == 'pdo') {
                $d['_hostname'] = $d['hostname'];

                $t = explode(':', $d['hostname']);
                $d['dbdriver'] = $t[0];

                $t = explode(';', $t[1]);
                $o = array();
                foreach($t as $s) {
                    $s = explode('=', $s);
                    $o[$s[0]] = $s[1];
                }
                $d['hostname'] = $o['host'];
            }
            $dump_file = APPPATH.'/install/init/init.sql';

            $output = '';
            $cmd = sprintf('mysqladmin -f -h"%s" -u"%s" -p"%s" drop "%s"', $d['hostname'], $d['username'], $d['password'], $d['database']);
            exec($cmd, $output);
            $this->_print($output);

            $output = '';
            $cmd = sprintf('mysqladmin -h"%s" -u"%s" -p"%s" create "%s"', $d['hostname'], $d['username'], $d['password'], $d['database']);
            exec($cmd, $output);
            $this->_print($output);
            $this->_print('Database "' . $d['database'] . '" created');

            $output = '';
            $cmd = sprintf('mysql -h"%s" -u"%s" -p"%s" "%s" < "%s"', $d['hostname'], $d['username'], $d['password'], $d['database'], $dump_file);
            exec($cmd, $output);
            $this->_print($output);
            $this->_print('Database "' . $d['database'] . '" initialized');

            exit;
        }
    }

    function _print($output) {
        if (!is_array($output)) {
            $output = array($output);
        }

        foreach ($output as $line) {
            echo $line . "\n";
        }
    }

    function _normalize_php($file) {

        @unlink($file.'.new');

        $warnings = array();
        $view_detected = false;

        $content = file_get_contents($file);

        $f = fopen($file, 'r');
        $line = fgets($f);

        if (trim($line) != '<?php') {
            $warnings['VIEW_DETECTED'] = 'NO PHP TAG OPENING, MAYBE VIEW?';
        }

        if (!isset($warnings['VIEW_DETECTED'])) {
            while($line = fgets($f)) {
                $line = trim($line);

                if (!empty($line)) {
                    $found = preg_match('/^\/\*/', $line);
                    if ($found) {
                        $warnings['FILE_COMMENT_DETECTED'] = 'FILE COMMENT IS DETECTED';
                    }
                    break;
                }

            }

            if (isset($warnings['FILE_COMMENT_DETECTED'])) {
                $found = false;
                $found_else = false;

                $ftellpos = ftell($f);

                while($line = fgets($f)) {
                    $line = trim($line);
                    $endl = preg_match('/\*\//', $line);
                    if ($endl) {
                        break;
                    }

                    $pos = strpos($line, 'This software is the proprietary information of PT Sagara Xinix Solusitama');
                    if ($pos !== FALSE) {
                        $found = true;
                        break;
                    }
                }

                fseek($f, $ftellpos);

                while($line = fgets($f)) {
                    $line = trim($line);
                    $endl = preg_match('/\*\//', $line);
                    if ($endl) {
                        break;
                    }

                    $pos = strpos($line, '@copyright');
                    if ($pos !== FALSE) {
                        $found_else = true;
                        break;
                    }
                }

                if (!$found) {
                    $warnings['XINIX_COPYRIGHT_NOT_FOUND'] = 'XINIX COPYRIGHT NOT FOUND';
                    if ($found_else) {
                        $warnings['OTHER_COPYRIGHT_FOUND'] = 'OTHER COPYRIGHT FOUND';
                    }
                }
            } else {
                $warnings['XINIX_COPYRIGHT_NOT_FOUND'] = 'XINIX COPYRIGHT NOT FOUND';
            }
        } else {
            // FIXME if view detected
        }
        fclose($f);

        if (isset($warnings['OTHER_COPYRIGHT_FOUND'])) {
            return;
        }

        if (!isset($warnings['VIEW_DETECTED'])) {
            $if = fopen($file, 'r');
            $of = fopen($file.'.new', 'w');

            if (!isset($warnings['OTHER_COPYRIGHT_FOUND'])) {
                $line = trim(fgets($if));
                fputs($of, $line."\n");

                $start = false;

                // default variables for copyright
                $nopath = str_replace(dirname($file).'/', '', $file);
                $created_time = '2011/11/21 00:00:00';
                $author = 'xinixman <hello@xinix.co.id>';
                $now_year = '2011';
                $copyright = "Copyright(c) $now_year PT Sagara Xinix Solusitama.  All Rights Reserved.";
                // default variables for copyright

                if (isset($warnings['FILE_COMMENT_DETECTED'])) {

                    $copyright_set = false;
                    $author_set = false;

                    while ($line = fgets($if)) {
                        $line = trim($line);
                        if (!$start && preg_match('/^\/\*/', $line)) {
                            $start = true;
                            continue;
                        } elseif ($start && preg_match('/\*\//', $line)) {
                            break;
                        }

                        if (isset($warnings['XINIX_COPYRIGHT_NOT_FOUND'])) {
                            $matches = array();
                            $match = preg_match('/^.+(@copyright\s+)(.*)$/i', $line, $matches);
                            if ($match) {
                                $copyright = $matches[2];
                                $copyright_set = true;
                            }

                            $matches = array();
                            $match = preg_match('/^.+(@author\s+)(.*)$/i', $line, $matches);
                            if ($match) {
                                $author = explode('<', $matches[2]);
                                if (count($author) > 1) {
                                    $x = explode('>', $author[1]);
                                    $author[1] = '<'.trim($x[0]).'>';
                                } else {
                                    $author[1] = '<'.$author[0].'@xinix.co.id>';
                                }
                                $author = trim(trim($author[0]).' '.trim($author[1]));
                                $author_set = true;
                            }
                        } else {
                            $lines = explode("\n", $content);
                            foreach($lines as $index => $line) {
                                $matches = '';
                                if (preg_match('/Copyright\(c\).*/', $line, $matches)) {
                                    $copyright = $matches[0];
                                }
                                if (strpos($line, 'History') !== FALSE) {
                                    break;
                                }
                            }

                            $match = preg_match('/(\d+\/\d+\/\d+\s+\d+(:\d+)+)\s+(.*)/', $lines[$index + 3], $matches);


                            if ($match) {
                                // $matches[1] = explode(' ', $matches[1]);
                                // $matches[1][0] = explode('/', $matches[1][0]);
                                // $matches[1][1] = explode(':', $matches[1][1]);
                                // if (count($matches[1][1]) < 3) {
                                //     $matches[1][1][] = '00';
                                // }
                                $matches[3] = explode(' ', trim($matches[3]));
                                $matches[3] = $matches[3][0];

                                $created_time = $matches[1] = '2011/11/21 00:00:00';//implode('/', $matches[1][0]).' '.implode(':', $matches[1][1]);
                                $author = $matches[3].' <'.$matches[3].'@xinix.co.id>';
                                $copyright_set = true;

                            }

                        }
                    }

                    if (!$copyright_set) {
                        $matches = array();
                        $lines = explode("\n", $content);
                        foreach($lines as $line) {
                            $matches = array();
                            $match = preg_match('/^.+(@copyright\s+)(.*)$/i', $line, $matches);
                            if ($match) {
                                $copyright = $matches[2];
                                $copyright_set = true;
                            }

                            $matches = array();
                            $match = preg_match('/^.+(@author\s+)(.*)$/i', $line, $matches);
                            if ($match) {
                                $author = explode('<', $matches[2]);
                                if (count($author) > 1) {
                                    $x = explode('>', $author[1]);
                                    $author[1] = '<'.trim($x[0]).'>';
                                } else {
                                    $author[1] = '<'.$author[0].'@xinix.co.id>';
                                }
                                $author = trim(trim($author[0]).' '.trim($author[1]));
                                $author_set = true;
                            }
                        }
                    }

                }

                $copyright_string = "
/**
 * $nopath
 *
 * @package     ark-php
 * @author      $author
 * @copyright   $copyright
 *
 * Created on $created_time
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * $created_time   $author
 *
 *
 */

";
                fputs($of, $copyright_string);
            }

            while ($line = fgets($if)) {
                $_line = trim($line);
                if (!empty($_line)) {
                    fputs($of, $line);
                    break;
                }
            }

            // copy rest of lines to new file
            while ($line = fgets($if)) {
                fputs($of, $line);
            }

            fclose($of);
            fclose($if);

            unlink($file);
            copy($file.'.new', $file);

            unlink($file.'.new');
        }
    }

    function _check_dir_for_normalize_file_header($dir) {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $full_path = $dir.'/'.$file;
                    if ($file[0] != '.' &&
                        $file != '..' &&
                        $file != '.git' &&
                        !preg_match('/^.\/system/', $full_path) &&
                        !preg_match('/^.\/themes/', $full_path) &&
                        !preg_match('/^.\/application\/logs/', $full_path) &&
                        !preg_match('/^.\/application\/third_party/', $full_path) &&
                        true) {
                        if (is_dir($dir.'/'.$file)) {
                            $this->_check_dir_for_normalize_file_header($dir.'/'.$file);
                        } else {
                            $ext = substr(strrchr($file,'.'),1);
                            switch($ext) {
                                case 'php':
                                    $this->_normalize_php($dir.'/'.$file);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
    }

    function normalize_file_header() {
        $this->_check_dir_for_normalize_file_header('.');
        exit;
    }

    function _post_controller_constructor() {
        if (!$this->_check_access()) {
            redirect('user/error?continue=' . current_url(), null, 303);
        }
    }

    function _check_access() {
        return $this->input->is_cli_request();
    }

}
