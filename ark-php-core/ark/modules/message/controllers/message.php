<?php

/**
 * message.php
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

class message extends App_Crud_Controller {


	function __construct() {
		parent::__construct();
	}

	function compose() {
		if ($_POST) {
			if ($this->_validate()) {
				$this->_model()->send($_POST);
			}
			redirect ('message/inbox') ;
			exit;
		}
	}

	function _on_status($value, $field, $row) {
		if ($field == 'created_time') {
			$value = format_short_datetime($value);
		}
		if ($row['user_data_status'] == 1) {
			$value = '<span style="font-weight: bold;color: #000">'.$value.'</span>';
		}

		return '<a href="'.site_url('message/detail/'.$row['id']).'" style="text-decoration: none; color: #666">'.$value.'</a>';
	}

	function inbox () {
		$this->load->library('pagination');

        $options = array(
        	'fields' => array('sender', 'subject', 'body', 'created_time'),
        	'formats' => array('callback__on_status', 'callback__on_status', 'callback__on_status', 'callback__on_status'),
        	'actions' => array(
        		'delete' => 'message/trash/inbox'
        	),
        );
        $this->_data['filter'] = $options['filter'] = $this->_get_filter()	;

        $per_page = $this->pagination->per_page;

        $count = 0;
        $this->_data['messages'] = $this->_model()->user_messages();

		$this->load->library('xgrid', $options, 'user_inbox');

        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'total_rows' => $count,
            'per_page' => $per_page,
        ));
	}

	function trash($from, $id) {
        if (!isset($id)) {
            show_404($this->uri->uri_string);
        }
        if (!empty($_GET['confirmed'])) {
            $id = explode(',', $id);
            $this->_model()->trash($id);
            redirect($this->_get_uri($from));
            exit;
        }

        $this->_data['from'] = $from;
        $this->_data['id'] = $id;
        $this->_data['ids'] = explode(',', $id);

        if (count($this->_data['ids']) == 1) {
            $this->_data['row_name'] = 'row #' . $id;
        }
    }

    function detail($id) {
    	$sql = "
    		SELECT m.*, TRIM(CONCAT(u.first_name, ' ', u.last_name)) sender
    		FROM message m
    		JOIN user u ON m.user_id = u.id
    	";
        $this->_data['data'] = $this->db->query($sql, array($id))->row_array();

        $user = $this->auth->get_user();
        $message_recipient = array(
        	'message_id' => $id,
        	'user_id' => $user['id'],
        	'status' => 2,
        );
        $this->_model()->before_save($message_recipient, $id);
        $this->db->where('message_id', $id)
        	->where('user_id', $user['id'])
        	->update('message_recipient', $message_recipient);
    }
}