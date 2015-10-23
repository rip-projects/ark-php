<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * crud_controller.php
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

/**
 * Description of crud_controller
 *
 * @author jafar
 */
class crud_controller extends app_base_controller {
    var $CAN_DELETE = false;
    var $CAN_SEARCH = true;

    function __construct() {
        parent::__construct();
        $this->load->helper(array('xform'));
    }

    function index() {
        redirect($this->_get_uri('listing'));
    }

    function detail($id) {
        $this->load->helper('format');
        $this->_data['fields'] = $this->_model()->list_fields(true);
        $this->_data['data'] = $this->_model()->get($id);
    }

    function add() {
        $this->_save();
    }

    function edit($id) {
        if (!isset($id)) {
            show_404($this->uri->uri_string);
        }

        $id = explode(',', $id);
        $this->_save($id[0]);
    }

    function delete($id) {
        if (!isset($id)) {
            show_404($this->uri->uri_string);
        }
        if (!empty($_GET['confirmed'])) {
            $id = explode(',', $id);
            $this->_model($this->_name)->delete($id);
            redirect($this->_get_uri('listing'));
        }

        $this->_data['id'] = $id;
        $this->_data['ids'] = explode(',', $id);

        if (count($this->_data['ids']) == 1) {
            $this->_data['row_name'] = 'row #' . $id;
        }
    }

    function trash($id) {
        if (!isset($id)) {
            show_404($this->uri->uri_string);
        }
        if (!empty($_GET['confirmed'])) {
            $id = explode(',', $id);
            $this->_model($this->_name)->trash($id);
            redirect($this->_get_uri('listing'));
        }

        $this->_data['id'] = $id;
        $this->_data['ids'] = explode(',', $id);

        if (count($this->_data['ids']) == 1) {
            $this->_data['row_name'] = 'row #' . $id;
        }
    }

    function _save($id = null) {
        $this->_view = $this->_name . '/show';

        if ($_POST) {
            if ($this->_validate()) {
                $_POST['id'] = $id;
                try {
                    $this->_model()->save($_POST, $id);
                    $referrer = $this->session->userdata('referrer');
                    if (empty($referrer)) {
                        $referrer = $this->_get_uri('listing');
                    }

                    add_info( ($id) ? l('Record updated') : l('Record added') );

                    if (!$this->input->is_ajax_request()) {
                        redirect($referrer);
                    }
                } catch (Exception $e) {
                    add_error(l($e->getMessage()));
                }
            }
        } else {
            if ($id !== null) {
                $this->_data['id'] = $id;
                $_POST = $this->_model()->get($id);
                if (empty($_POST)) {
                    show_404($this->uri->uri_string);
                }
            }
            $this->load->library('user_agent');
            $this->session->set_userdata('referrer', $this->agent->referrer());
        }
        $this->_data['fields'] = $this->_model()->list_fields(true);
    }

    function listing($offset = 0) {
        $this->load->library('pagination');

        $config_grid = $this->_config_grid();
        $config_grid['sort'] = $this->_get_sort();

        $filter = $this->_get_filter();

        $count = 0;
        $this->_data['data'] = array();
        $this->_data['data']['items'] = $this->_model()->listing($filter, $config_grid['sort'], $this->pagination->per_page, $offset, $count);
        $this->_data['filter'] = $filter;
        $this->load->library('xgrid', $config_grid, 'listing_grid');

        $this->load->library('pagination');
        $param = array(
            'total_rows' => $count,
            'per_page' => $this->pagination->per_page,
        );
        if (!empty($_GET)) {
            $param['suffix'] = '?'.http_build_query($_GET, '', '&');
        }
        $this->pagination->initialize($param);

    }

    /**
     * Get sort for current controller
     * @return mixed sort data
     */
    function _get_sort($sort_name = '') {
        $sort_name = (empty($sort_name)) ? $this->_name: $sort_name;

        $sort = array();
        if (isset($_GET['sort'])) {
            if (empty($_GET['sort'])) {
                $this->session->unset_userdata('sort::'.$sort_name);
            } else {
                $ss = explode(',', $_GET['sort']);
                foreach ($ss as $s) {
                    $s = explode(':', trim($s));
                    $sort[$s[0]] = (!empty($s[1])) ? $s[1] : 'asc';
                }
                $this->session->set_userdata('sort::'.$sort_name, $sort);
            }
            redirect($this->_get_uri($this->uri->rsegments[2]));
        } else {
            $sort = $this->session->userdata('sort::'.$sort_name);
        }
        return $sort;
    }

    /**
     * Get filter for current controller
     * @return mixed filter data
     */
    function _get_filter($filter_name = '') {
        $filter = '';

        if (empty($filter_name)) {
            $filter_name = $this->_name;
        }

        if (isset($_POST['_']) && $_POST['_'] == 'filter') {

            /**
             * if there is post data with method value filter then try to get
             * data and excluding _ field and empty fields
             */
            unset($_POST['_']);
            foreach ($_POST as $key => $value) {
                if (empty($value)) {
                    unset($_POST[$key]);
                }
            }

            $this->session->set_userdata('filter::'.$filter_name, $_POST);
            redirect($this->_get_uri($this->uri->rsegments[2]));
        } elseif(isset($_GET['q'])) {

            /**
             * if there is get data with key 'q' but empty then remove filter
             * from session otherwise update filter with new value
             */
            if (empty($_GET['q'])) {
                $this->session->unset_userdata('filter::' . $filter_name);
            } else {
                $this->session->set_userdata('filter::'.$filter_name, array(
                    'q' => $_GET['q']
                ));
            }
            redirect($this->_get_uri($this->uri->rsegments[2]));
        } else {
            $filter = $this->session->userdata('filter::' . $filter_name);

            /**
             * if there is filter in session then apply this filter in key q to
             * fields defined in configuration (fallback to all fields in config )
             */
            if (!empty($filter) and !empty($filter['q'])) {
                $config_grid = $this->_config_grid();
                $fields = (!empty($config_grid['filters'])) ? $config_grid['filters'] : $config_grid['fields'];

                foreach ($fields as $field) {
                    if (!array_key_exists($field, $filter)) {
                        $filter[$field] = $filter['q'];
                    }
                }
            }
        }

        return $filter;
    }

    function _config_grid() {
        $fields = array_keys($this->_model($this->_name)->list_fields(true));
        $config = array(
            'fields' => $fields,
            'formats' => array('row_detail'),
            'sorts' => $fields,
            'actions' => array(
                'edit' => $this->_get_uri('edit'),
                'trash' => $this->_get_uri('trash'),
            ),
        );

        if ($this->CAN_DELETE) {
            $config['actions']['delete'] = $this->_get_uri('delete');
        }

        return $config;
    }

    function import($type = 'csv') {
        $config_grid = $this->_get_exim_config();
        $this->_data['config'] = $config_grid;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $this->auth->get_user();
            $config['upload_path'] = './data/import/';
            $config['allowed_types'] = $type;
            $config['max_size'] = '100';
            $config['max_width'] = '1024';
            $config['max_height'] = '768';
            $config['file_name'] = sprintf('%s_%s.%s', $user['id'], date('YmdHis'), $type);

            $this->load->library('upload', $config);
            $filename = $config['upload_path'] . '/' . $config['file_name'];

            if (!$this->upload->do_upload()) {
                add_error($this->upload->error_msg);
            } else {
                $data = $this->upload->data();
                $this->_data['info'] = l('Upload success');

                $handle = fopen($filename, 'r');
                $index = 0;
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    if ($index < 1) {
                        $index++;
                    } else {
                        $row = array();
                        $find = array(',', ';');
                        foreach ($config_grid['import'] as $key => $field) {
                            $row[$field] = str_replace($find, '', $data[$key]);
                        }
                        try {
                            $tmp = $this->_model()->_db()->db_debug;
                            $this->_model()->_db()->db_debug = false;
                            $this->_model()->save($row);
                            $this->_model()->_db()->db_debug = $tmp;
                        } catch (Exception $e) {
                            log_message('error', print_r($e, 1));
                        }
                    }
                }
                fclose($handle);

                redirect($this->_get_uri('listing'));
            }
        }
    }

    function export($type = 'csv') {
        $config_grid = $this->_get_exim_config();
        $this->load->helper('date');
        $outstr = NULL;

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment;Filename=' . strtolower($this->_name) . '.' . $type);

        echo humanize($this->_name) . ",\n";
        $datestring = "%d %M %Y - %h:%i %a";
        $time = time();
        echo mdate($datestring, $time) . ",\n";

        echo implode(',', $config_grid['import_names']) . "\n";

        $data = $this->_model()->import($config_grid['import']);

        foreach ($data as $row) {
            echo implode(',', $row) . "\n";
        }
        exit;
    }

    function rpc_get() {
        $q = (empty($_GET['q'])) ? '' : $_GET['q'];
        $limit = (empty($_GET['limit'])) ? 15 : $_GET['limit'];

        if (empty($q)) {
            $this->_data['data'] = array();
        } else {
            $fields = array_keys($this->_model()->list_fields(true));
            $this->_data['data'] = $this->_model()->_db()->like($fields[0], $q, "both")->get($this->_name)->result_array();

        }


    }

    function import_example($type = 'csv') {
        $config_grid = $this->_get_exim_config();

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment;Filename=example_' . strtolower($this->_name) . '.' . $type);

        echo implode(',', $config_grid['import_names']) . "\n";
        foreach ($config_grid['import_examples'] as $ex) {
            echo implode(',', $ex) . "\n";
        }
        exit;
    }

    function _get_exim_config() {
        $config_grid = $this->_config_grid();

        if (empty($config_grid['import'])) {
            $config_grid['import'] = $config_grid['fields'];
        }

        if (empty($config_grid['import_names'])) {
            foreach ($config_grid['import'] as $field) {
                $config_grid['import_names'][] = humanize($field);
            }
        }

        if (empty($config_grid['import_examples'])) {
            for ($i = 0; $i < 10; $i++) {
                $ex = array();
                foreach ($config_grid['import'] as $field) {
                    $ex[] = $field . ' ' . $i;
                }
                $config_grid['import_examples'][] = $ex;
            }
        }

        return $config_grid;
    }

}

