<?php

/**
 * Xservice.php
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
 * Description of Xservice
 *
 * @author jafar
 */
class Xservice {

    var $inbox = 'chat_inbox';
    var $outbox = 'chat_outbox';

    function __construct($params = array()) {
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
    }

    function request($request) {
        return $request;
    }

    function run() {
        $CI = &get_instance();

        while (1) {
            $inbox = $CI->_model($this->inbox)->find();

            foreach ($inbox as $row) {
                $response = $this->request($row['body']);
                if (!is_array($response)) {
                    $response = array($response);
                }

                foreach ($response as $line) {
                    $CI->_model($this->outbox)->save(array(
                        'account' => $row['account'],
                        'to' => $row['from'],
                        'body' => $line . '',
                    ));
                }
            }

            $CI->_model($this->inbox)->truncate();
            sleep(1);
        }
    }

}