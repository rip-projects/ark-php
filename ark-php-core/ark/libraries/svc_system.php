<?php

/**
 * svc_system.php
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
 * Description of svc_system
 *
 * @author jafar
 */
require_once ARCHPATH . 'libraries/Xservice.php';

class svc_system extends Xservice {

    function request($request) {
//        $resp = '';
//        for ($i = strlen($request) - 1; $i >= 0; $i--) {
//            $resp .= $request[$i];
//        }
//        return $resp;
        return 'Jangan lupa untuk contreng reekoheek!';
    }

}
