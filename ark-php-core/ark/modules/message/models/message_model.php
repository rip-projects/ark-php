<?php

/**
 * message_model.php
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

class message_model extends App_Base_Model {

	function user_messages(){
		$CI =  &get_instance();
		$user = $CI->auth->get_user();
		$sql = "
			SELECT m.*, TRIM(CONCAT(u.first_name, ' ', u.last_name)) sender, mr.status user_data_status
			FROM message m
			JOIN message_recipient mr ON m.id = mr.message_id
			JOIN user u ON m.user_id = u.id
			WHERE mr.user_id = ?
			AND mr.status > 0
			ORDER BY m.created_time DESC
		";
		return $this->db->query($sql,array($user['id']))->result_array();
	}

	function send($data) {
		$CI = &get_instance();
		$user = $CI->auth->get_user();
		$message = array(
			'subject' => $data['subject'],
			'body' => $data['body'],
			'user_id' => $user['id'],
		);
		$this->before_save($message);
		$this->db->insert('message', $message);
		$id = $this->db->insert_id();

		foreach($data['tos'] as $to) {
			if (!empty($to)) {
				$recipient = array(
					'message_id' => $id,
					'user_id' => $to,
				);
				$this->before_save($recipient);
				$this->db->insert('message_recipient', $recipient);
			}
		}
	}
}

