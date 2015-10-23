<?php

class htmltopdf {
    var $exe = '';
    var $template = '';
    var $tmp_path = '';
    var $ci;
    var $ext = 'xhtml';
    var $theme_url = '';
    var $debug = false;

    function __construct($params = null) {
        $this->ci = &get_instance();
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

        if (empty($this->tmp_path)) {
            $this->tmp_path = sys_get_temp_dir();
        }

        if (empty($this->theme_url)) {
            $this->theme_url = getcwd().'/themes/'.$this->ci->config->item('theme').'/';
        }
    }

    function show($data) {
        $data['CI'] = &$this->ci;
        $data['self'] = &$this;

        $dir_name = $this->tmp_path . '/html2pdf';

        @mkdir($dir_name, true);
        $t_file = tempnam($dir_name, 'html2pdf_');

        $template_string = $this->ci->load->view($this->template, $data, true);

        if ($this->debug) {
            echo $template_string;
            exit;
        }


        $f = fopen($t_file.'.'.$this->ext, 'w');
        fwrite($f, $template_string);
        fclose($f);

        exec(sprintf('%s --redirect-delay 10000 --print-media-type "%s" "%s"', $this->exe, $t_file.'.'.$this->ext, $t_file.'.pdf'));

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="the.pdf"');
        header('Content-Length: ' . filesize($t_file.'.pdf'));
        echo file_get_contents($t_file.'.pdf');
        exit;
    }

    function theme_url($uri) {
        return theme_url($uri);
    }
}