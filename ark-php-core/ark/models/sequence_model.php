<?php

/**
 * sequence_model.php
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
 * Description of sequence_model
 *
 * @author jafar
 */
class Sequence_model extends App_Base_Model {

    function get_sequence($key) {
        $row = $this->db->query('SELECT * FROM sequence WHERE skey = ?', array($key))->row_array();
        if (empty($row)) {
            $row = array(
                'skey' => $key,
                'sequence' => 1,
            );
            $id = NULL;
        } else {
            $row['sequence'] = $row['sequence'] + 1;
            $id = $row['id'];

        }
        $this->save($row, $id);
        return $row['sequence'];
    }

}

