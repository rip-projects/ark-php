<?php

/**
 * workflow_controller.php
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
class workflow_controller extends app_crud_controller {

    var $payment_enabled = -1;
    var $reply_enabled = -1;
    var $module_enabled = 0;
    var $pre_data_enabled = 0;

    var $_name;
    var $_action;

    function __construct() {
        parent::__construct();

        $this->_name = get_class($this);
        $this->_action = $this->_model('action')->get(array('action' => $this->_name));
        $this->module_enabled = (isset($this->_action['module_enabled'])) ? $this->_action['module_enabled'] : $this->module_enabled;

        $this->load->helper('app');

    }

    function modules($id = '') {
        if (empty($id)) {
            redirect($this->uri->rsegments[1] . '/listing');
        }

        if ($_POST) {
            $sql = "
                SELECT da.*, a.name, a.action
                FROM disposition_action da
                JOIN action a ON da.action_id = a.id
                WHERE da.status < 3 AND parent = (
                    SELECT id FROM disposition_action WHERE action_id = ? AND ref = ?
                )
            ";
            $param = array($this->_action['id'], $id);
            $modules = $this->db->query($sql, $param)->result_array();

            if (!empty($modules)) {
                add_error(l('Modules have not been finished yet'));
            } else {
                if (empty($this->workflow['pages'])) {
                    redirect($this->_name . '/summary/' . $id);
                } else {
                    // redirect($this->_name . '/' . $this->workflow['pages'][0] . '/' . $id);
                    redirect($this->action_uri . '/' . $id);
                }
            }
        }

        $user = $this->auth->get_user();

        $sql = "
            SELECT da.*, a.name, a.action
            FROM disposition_action da
            JOIN action a ON da.action_id = a.id
            JOIN disposition d ON da.disposition_id = d.id
            WHERE parent = (
                SELECT id FROM disposition_action WHERE action_id = ? AND ref = ?
            )
        ";

        $param = array($this->_action['id'], $id);
        $this->_data['modules'] = $this->db->query($sql, $param)->result_array();

        $config = array(
            'fields' => array('name', 'status'),
            'names' => array('Action'),
            'formats' => array('callback__act', 'param_short(disposition_status)'),
            'show_checkbox' => false,
            'actions' => array(),
        );
        $this->load->library('xgrid', $config, 'module_list');
    }

    function _act($value, $f, $row) {
        return '<a href="'.site_url($row['action'].'/index/'.$row['ref']).'">'.$value.'</a>';
    }

    function _set_referer() {
        $this->session->set_userdata('referer:workflow:'.$this->_name, $_SERVER['HTTP_REFERER']);
    }

    function _get_referer() {
        return $this->session->userdata('referer:workflow:'.$this->_name);
    }

    function index($id = '') {
        $this->_set_referer();
        if (empty($id)) {
            redirect($this->uri->rsegments[1] . '/listing');
        }

        if ($this->module_enabled) {
            if ($this->pre_data_enabled) {
                redirect($this->_name.'/pre_data/'.$id);
            } else {
                redirect($this->_name.'/modules/'.$id);
            }
        }

        if (empty($this->workflow['pages'])) {
            redirect($this->_name . '/summary/' . $id);
        } else {
            redirect($this->_name . '/' . $this->workflow['pages'][0] . '/' . $id);
        }

    }

    function pre_data($id) {
        if ($_POST) {
             $sql = "
                SELECT da.*, a.name, a.action
                FROM disposition_action da
                JOIN action a ON da.action_id = a.id
                WHERE da.status < 3 AND parent = (
                    SELECT id FROM disposition_action WHERE action_id = ? AND ref = ?
                )
            ";
            $param = array($this->_action['id'], $id);
            $modules = $this->db->query($sql, $param)->result_array();
            foreach($modules as $module) {
                $this->_model($module['action'])->save(array('vessel_id' => $_POST['vessel_id']),$module['ref']);
            }
        }

        $this->_state_action($id);

    }

    function _back_button() {
        if ($this->uri->rsegments[2] == 'pre_data') {
            return;
        } elseif ($this->uri->rsegments[2] == 'modules' && !$this->pre_data_enabled) {
            return;
        }

        $index = $this->_get_index();
        if ($index == -1 || $index > 0) {
            $html = '<input type="submit" name="_action" value="' . l('Back') . '" id="btn-workflow-back" style="float:right" />';
            return $html;
        } elseif ($index == 0 && ($this->pre_data_enabled || $this->module_enabled)) {
            $html = '<input type="submit" name="_action" value="' . l('Back') . '" id="btn-workflow-back" style="float:right" />';
            return $html;
        }
    }

    function _next_button() {
        if ($this->uri->rsegments[2] == 'pre_data' || $this->uri->rsegments[2] == 'modules') {
            return '<input type="submit" name="_action" value="' . l('Next') . '" id="btn-workflow-next" class="default" style="float:right" />';
        }

        $index = $this->_get_index();
        if ($index > -1 && $index < count($this->workflow['pages']) - 1) {
            $html = '<input type="submit" name="_action" value="' . l('Next') . '" id="btn-workflow-next" class="default" style="float:right" />';
            return $html;
        }
    }

    function _summary_button() {
        if ($this->uri->rsegments[2] !== 'summary') {
            $html = '<input type="submit" name="_action" value="' . l('Summary') . '" id="btn-workflow-summary" class="default" style="float:right" />';
            return $html;
        }
    }

    function _submit_button() {
        if ($this->uri->rsegments[2] == 'summary') {
            $html = '<input type="submit" name="_action" value="' . l('Submit') . '" id="btn-workflow-submit" style="float:right" />';
            return $html;
        }
    }

    function _buttons() {
        echo '<div style="display: table">';
        echo '<a style="float: left" href="' . $this->_get_referer() . '" class="button">' . l('Back to Listing') . '</a>';
        echo '<div style="display: table; clear: none">';
        echo $this->_submit_button();
        echo $this->_summary_button();
        echo $this->_next_button();
        echo $this->_back_button();
        echo '</div>';
        echo '</div>';
    }

    function _back_uri() {
        $index = $this->_get_index();

        if ($this->uri->rsegments[2] == 'modules') {
            if ($this->pre_data_enabled) {
                $back = 'pre_data';
            }
        } elseif ($index == 0) {
            if ($this->module_enabled) {
                $back = 'modules';
            } elseif ($this->pre_data_enabled) {
                $back = 'pre_data';
            }
        }
        if (empty($back)) {
            if ($index < 0) {
                $index = count($this->workflow['pages']);
            }
            $back = $this->workflow['pages'][$index - 1];
        }
        return $this->_name . '/' . $back;
    }

    function _next_uri() {
        if ($this->uri->rsegments[2] == 'pre_data') {
            if ($this->module_enabled) {
                $next = 'modules';
            }
        }
        if (empty($next)) {
            $next = $this->workflow['pages'][$this->_get_index() + 1];
        }
        return $this->_name . '/' . $next;
    }

    function _get_index() {
        if (!empty($this->workflow['pages'])) {
            foreach ($this->workflow['pages'] as $key => $value) {
                if ($value === $this->uri->rsegments[2]) {
                    return $key;
                }
            }
        }

        return -1;
    }

    function _valid_number_value($value) {
        foreach($value as $val) {
            if (!is_numeric($val)) {
                $this->form_validation->set_message('_valid_number_value', l('The %s field must be valid number value.'));
                return FALSE;
            }
        }
        return TRUE;
    }

    function _mail_code_required($value) {

        if ($this->reply_enabled === 1) {
            $_POST['use_reply'] = 1;
        }
        $_POST['use_reply'] = (empty($_POST['use_reply'])) ? 0 : $_POST['use_reply'];

        if ($_POST['use_reply'] && empty($value)) {
            $this->form_validation->set_message('_mail_code_required', l('The %s field required.'));
            return FALSE;
        }
        return TRUE;
    }

    function summary($id = '') {

        if ($_POST) {

            if ($_POST['_action'] == l('Back')) {
                redirect($this->action_uri . '/' . $id);
            }

            $validation = array(
                'payment_type' => array('required|callback__valid_number_value'),
                'payment_item' => array('required|callback__valid_number_value'),
                'amount' => array('required|callback__valid_number_value'),
                'mail_code_id' => array('callback__mail_code_required'),
            );

            if ($this->payment_enabled === 1) {
                $_POST['use_payment'] = 1;
            }

            if ($this->reply_enabled === 1) {
                $_POST['use_reply'] = 1;
            }

            if (empty($_POST['use_payment'])) {
                unset($validation['payment_type']);
                unset($validation['payment_item']);
                unset($validation['amount']);
            } else {
                foreach($_POST['payment_type'] as $k => $v) {
                    if ($v === '') {
                        unset($_POST['payment_type'][$k]);
                        unset($_POST['payment_item'][$k]);
                        unset($_POST['amount'][$k]);
                    }
                }
                if (empty($_POST['payment_type'])) $_POST['payment_type'] = '';
                if (empty($_POST['payment_item'])) $_POST['payment_item'] = '';
                if (empty($_POST['amount'])) $_POST['amount'] = '';
            }
            $this->_validation['summary'] = (!empty($this->_validation['summary'])) ? array_merge($validation, $this->_validation['summary']) : $validation;

            if ($this->_validate()) {
                $this->db->trans_start();

                $rowdata = $this->_model()->get($id);

                $data = $this->db->query('
                        SELECT t.*, i.from
                        FROM '.$this->_model()->_name.' t
                        LEFT JOIN inbox i ON t.inbox_id = i.id
                        WHERE t.id = ?
                    ', $id)->row_array();
                $user = $this->auth->get_user();
                $action = $this->db->where('action', get_class($this))->get('action')->row_array();

                $to = $data['from'];

                if (!empty($_POST['use_payment'])) {
                    foreach($_POST['payment_type'] as $k => $payment_type) {
                        $price_item = $this->db->where('id', $_POST['payment_item'][$k])->get('price_list_item')->row_array();

                        $row = array(
                            'organization_id' => $data['organization_id'],
                            'inbox_id' => $data['inbox_id'],
                            'disposition_id' => $rowdata['disposition_id'],
                            'vessel_id' => (isset($data['vessel_id'])) ? $data['vessel_id'] : 0,
                            'price_list_id' => $_POST['payment_type'][$k],
                            'price_item_id' => $_POST['payment_item'][$k],
                            'price' => $price_item['price'],
                            'amount' => $_POST['amount'][$k],
                            'total' => $price_item['price'] * $_POST['amount'][$k],
                            'action_ref' => $this->_name.':'.$id,
                            'currency' => $price_item['currency'],
                        );

                        $this->_model('payment')->save($row);
                    }
                }

                $out_id = 0;
                if (!empty($_POST['use_reply'])) {
                    $dosir = $this->_model('outbox')->new_dosir($_POST['mail_code_id']);

                    $data_outbox = array(
                       'organization_id' => $data['organization_id'],
                       'division_id' => $user['division_id'],
                       'concerning' => $action['name'],
                       'to' => $to,
                       'to_date' => date('Y-m-d'),
                       'dosir_id' => $dosir['id'],
                       'mail_no' => $dosir['mail_no'],
                   );

                   $out_id = $this->_model('outbox')->save($data_outbox);
                }

                // $data = $this->get_additional_data($id, $_POST);

                $data = array();
                $data['status'] = 3;
                $data['outbox_id'] = $out_id;
                $this->_model()->save($data, $id);

                $disp_action = $this->db->where(array(
                    'disposition_id' => $rowdata['disposition_id'],
                    'action_id' => $action['id'],
                    'ref' => $id,
                ))->get('disposition_action')->row_array();

                $data = array('status' => 4);
                $this->_model('disposition')->before_save($data, $rowdata['disposition_id']);
                $this->db->where('id', $disp_action['id'])->update('disposition_action', $data);

                $notdone = $this->db
                    ->where('disposition_id', $rowdata['disposition_id'])
                    ->where('status <', 4)
                    ->get('disposition_action')->result_array();

                if (empty($notdone)) {
                    $this->db->where('id', $rowdata['disposition_id'])->update('disposition', $data);
                }

                $this->db->trans_complete();
                redirect($this->_get_referer());
            }

        }

        $this->load->helper('format');

        $this->_data['data'] = $this->_model()->get($id);

        if (!empty($this->_data['data']['vessel_id'])) {
            $this->_data['vessel'] = $this->_model('vessel')->get($this->_data['data']['vessel_id']);
        }

        $user = $this->auth->get_user();
        $org_id = $user['organization'][0]['org_id'];
        $div_id = $user['division_id'];

        $sql = '
            SELECT *
            FROM mail_code
            WHERE organization_id = ?
            AND division_id = ?
            ORDER BY name ASC
        ';
        $codes = $this->db->query($sql, array($org_id, $div_id))->result_array();
        $this->_data['mail_code_items'] = array('' => l('(Please select)'));
        foreach ($codes as $code) {
            $this->_data['mail_code_items'][$code['id']] = $code['code'] . ' - ' . $code['name'];
        }

        $this->_data['payment_type_options'] = array('' => l('(Please select)'));
        $payments = $this->db->get('price_list')->result_array();
        foreach($payments as $payment) {
            $this->_data['payment_type_options'][$payment['id']] = $payment['name'];
        }

    }

    function _state_action($id) {
        if ($_POST) {

            if ($this->_validate()) {
                unset($_POST['_action']);
                $_POST['status'] = 2;
                $this->_model()->save($_POST, $id);
                redirect($this->action_uri . '/' . $id);
            }
        } else {
            $_POST = $this->_model()->get($id);
        }
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['actions'] = array(
            'input_data' => $this->_get_uri('index'),
            'print_pdf' => $this->_get_uri('print_pdf'),
        );
        $config['custom_script'] = "
            var that = this;
            $(function() {
                $(that).find('tr.grid_row').each(function() {
                    var tdLen = $(this).find('td').length;
                    var status = $(this).find('input.f-status').val();

                    if (status >= 3) {
                        $(this).find('td.submenu .input_data').hide();
                    } else {
                        $(this).find('td.submenu .print_pdf').hide();
                    }
                });
            });
        ";
        return $config;
    }

    function _status($value) {
        return format_param_short($value, 'status') . '<input type="hidden" class="f-status" value="' . $value . '"/>';
    }

    function _payment_status($value) {
        return format_param_short($value, 'payment_status') . '<input type="hidden" class="f-pstatus" value="' . $value . '"/>';
    }

    function listing($offset = 0) {
        $this->load->library('pagination');

        $config_grid = $this->_config_grid();
        $field_count = count($config_grid['fields']);
        if ($this->payment_enabled != 0) {
            $config_grid['fields'][$field_count] = 'payment_status';
            $config_grid['fields'][$field_count+1] = 'status';
            $config_grid['formats'][$field_count] = 'callback__payment_status';
            $config_grid['formats'][$field_count+1] = 'callback__status';
        } else {
            $config_grid['fields'][$field_count] = 'status';
            $config_grid['formats'][$field_count] = 'callback__status';
        }
        $config_grid['sort'] = $this->_get_sort();
        $config_grid['filter'] = $this->_get_filter();
        $per_page = $this->pagination->per_page;

        $method = $config_grid['method'];

        $count = 0;
        $this->_data['data']['items'] = $this->_model()->$method($config_grid['filter'], $config_grid['sort'], $per_page, $offset, $count);
        $this->_data['filter'] = $config_grid['filter'];
        $this->load->library('xgrid', $config_grid, 'listing_grid');
        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'total_rows' => $count,
            'per_page' => $per_page,
        ));
    }

    function _breadcrumb($is_module_page = false) {
        $index = $this->_get_index();

        $pages = array();
        if (!empty($this->workflow['pages'])) {
            foreach($this->workflow['pages'] as $k => $page) {
                $pages[] = '<li'.(($index == $k) ? ' class="active"' : '').'><a href="'.site_url($this->uri->rsegments[1].'/'.$page.'/'.$this->uri->rsegments[3]).'">'.$this->workflow['names'][$k].'</a></li>';
            }
        }

        $module_page = ($this->module_enabled) ? '<li'.(($this->uri->rsegments[2] == 'modules') ? ' class="active"' : '').'><a href="'.site_url($this->uri->rsegments[1].'/modules/'.$this->uri->rsegments[3]).'">'.l('Modules').'</a></li>' : '';

        $summary_page = '<li'.(($this->uri->rsegments[2] == 'summary') ? ' class="active"' : '').'><a href="'.site_url($this->uri->rsegments[1].'/summary/'.$this->uri->rsegments[3]).'">'.l('Summary').'</a></li>';

        return '<ul class="breadcrumb">
                <li><a href="'.base_url().'">'.l('Home').'</a></li>
                <li><a href="'.site_url($this->uri->rsegments[1]).'">'.l(humanize(get_class($this))).'</a></li>
                '.$module_page.'
                '.implode("\n", $pages).'
                '.$summary_page.'
            </ul>
        ';
    }

    function _post_controller_constructor() {
        if ($_POST) {
            switch ($_POST['_action']) {
                case l('Back'):
                    $this->action_uri = $this->_back_uri();
                    redirect($this->action_uri.'/'.$this->uri->rsegments[3]);
                    break;
                case l('Next'):
                    $this->action_uri = $this->_next_uri();
                    break;
                case l('Summary'):
                    $this->action_uri = $this->_name . '/summary';
                    break;
            }
        }

        if ($this->uri->rsegments[2] == 'summary') {
            $this->_view = 'summary_placeholder';
        }
        parent::_post_controller_constructor();
    }

}