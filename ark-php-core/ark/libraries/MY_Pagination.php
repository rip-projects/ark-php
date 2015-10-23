<?php

/**
 * MY_Pagination.php
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
 * Description of MY_Pagination
 *
 * @author jafar
 */

class MY_Pagination extends CI_Pagination {

    var $per_pages = array(10);
    var $per_page_changer_prefix = 'Show ';

    function __construct($params = array()) {
        parent::__construct($params);

        $CI = &get_instance();

        if (isset($_GET['per_page'])) {
            $CI->session->set_userdata('per_page', $_GET['per_page']);
            $this->per_page = $_GET['per_page'];
//            redirect($this->base_url);
        } else {
            $per_page = $CI->session->userdata('per_page');
            if (!empty($per_page)) {
                $this->per_page = $per_page;
            }
        }
    }

    function initialize($params = array()) {
        $CI = &get_instance();
        if (empty($params['base_url'])) {
            $listing_pos = array_search('listing', $CI->uri->segments);
            $this->base_url = site_url($CI->_get_uri('listing'));
            $this->uri_segment = $listing_pos + 1;
        }

        parent::initialize($params);
    }

    function per_page_changer() {
        $CI = &get_instance();
        $current_per_page = $CI->session->userdata('per_page');

        if (empty($current_per_page)) {
            $current_per_page = $this->per_pages[0];
            $CI->session->set_userdata('per_page', $current_per_page);
        }

        $params = array(
            'CI' => &$CI,
            'self' => &$this,
            'current_per_page' => $current_per_page,
        );

        return $CI->load->view('libraries/pagination_per_page_changer', $params, true);
    }

    function create_links() {
        $links = parent::create_links();
        if ($links) {
            $links = "
                <div class=\"pagination\">
                    <span style=\"margin-left:5px\" class=\"pull-right\"> of ".(ceil($this->total_rows / $this-> per_page))."</span>
                    <div class=\"pull-right\">" . $links . "</div>
                    <span style=\"margin-right: 5px\" class=\"pull-right\">Page</span>
                </div>";
        }
        return $links;
    }

}