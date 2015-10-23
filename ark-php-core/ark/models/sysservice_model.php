<?php

/**
 * sysservice_model.php
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
 * Description of sys_service_model
 *
 * @author jafar
 */
class sysservice_model extends App_Base_Model {

    function start($id) {
        if ($this->status($id)) {
            return 0;
        }

        $CI = &get_instance();
        $service = $this->get($id);

        $uri = explode(':', $service['uri']);
        if ($uri[0] == 'context') {
            $cmd = 'php index.php ' . $uri[1];
        } else {
            $cmd = $uri[1];
        }

        $cmd = 'nohup ' . $cmd . '  > ' . $CI->log->get_log_path() . 'service_log_' . $id . '.php 2>&1 & echo $!';
        exec($cmd, $output);

        if (!empty($output)) {
            $this->save(array('pid' => $output[0]), $id);
        }

        return $this->status($id);
    }

    function stop($id) {
        $CI = &get_instance();
        $service = $this->get($id);
        if (empty($service) || $service['pid'] == 0) {
            return 0;
        }
        exec('kill -9 ' . $service['pid']);
        return $this->status($id);
    }

    function status($id, &$updated = null) {
        $CI = &get_instance();
        $service = $this->get($id);
        if (empty($service) || $service['pid'] == 0) {
            return 0;
        }

        $output = '';
        exec("ps ax | awk '{print $1}' | grep " . $service['pid'], $output);
        if (!empty($output)) {
            $updated = array('status' => 1);
            $this->save($updated, $id);
            return 1;
        } else {
            $updated = array('status' => 0, 'pid' => 0);
            $this->save($updated, $id);
            return 0;
        }
    }

}
