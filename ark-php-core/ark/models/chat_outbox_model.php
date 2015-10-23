<?php

/**
 * chat_outbox_model.php
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
 * Description of chat_outbox_model
 *
 * @author jafar
 */
class chat_outbox_model extends App_Base_Model {

    function save($data, $id = null) {
        if (empty($id)) {
            $this->_db()->insert($this->_name, $data);
            $err_no = $this->_db()->_error_number();
            $err_msg = $this->_db()->_error_message();
            if (empty($err_no)) {
                return $this->_db()->insert_id();
            } else {
                log_message('warn', $err_no . ' : ' . $err_msg);
                throw new RuntimeException($err_msg, $err_no);
            }
        } else {
            $this->_db()->where('id', $id);
            $this->_db()->update($this->_name, $data);
            $err_no = $this->_db()->_error_number();
            $err_msg = $this->_db()->_error_message();
            if (empty($err_no)) {
                return $id;
            } else {
                log_message('warn', $err_no . ' : ' . $err_msg);
                throw new RuntimeException($err_msg, $err_no);
            }
        }
    }

}

?>
