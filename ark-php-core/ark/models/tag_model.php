<?php

/**
 * tag_model.php
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
 * Description of tag_model
 *
 * @author jafar
 */
class tag_model extends App_Base_Model {

    function add($tag_name) {

        $data = array(
            'name' => $tag_name
        );
        $obj = $this->get($data);

        if (empty($obj)) {
            return $this->save($data);
        } else {
            return $obj['id'];
        }
    }

}
