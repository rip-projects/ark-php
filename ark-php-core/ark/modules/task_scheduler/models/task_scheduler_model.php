<?php

/**
 * task_scheduler_model.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
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
 * Description of task_scheduler_model
 *
 * @author jafar
 */
class task_scheduler_model extends App_Base_Model {

    function get_next_time($task) {
        $hours = $minutes = array();

        if ($task['hour'] == '*') {
            for ($i = 0; $i < 24; $i++) {
                $hours[] = $i;
            }
        } else {
            $hours = explode(',', $task['hour']);
        }

        if ($task['minute'] == '*') {
            for ($i = 0; $i < 60; $i++) {
                $minutes[] = $i;
            }
        } else {
            $minutes = explode(',', $task['minute']);
        }

        $now_str = date('H:i');
        $now = intval(date('Hi'));
        $next = '';
        $first = '';

        foreach ($hours as $hour) {
            foreach ($minutes as $minute) {
                $t_str = sprintf('%02d:%02d', $hour, $minute);
                $t = intval(sprintf('%02d%02d', $hour, $minute));

                if (empty($first)) {
                    $first = $t_str;
                }
                if ($t > $now) {
                    $next = $t_str;
                    break 2;
                }
            }
        }

        if (empty($next)) {
            $next = date('Y-m-d', mktime() + ( 60 * 60 * 24)) . ' ' . $first . ':00';
        } else {
            $next = date('Y-m-d') . ' ' . $next . ':00';
        }

        return $next;
    }

    function cron() {
        $start = microtime();
        $result = $this->db->query('SELECT * FROM task_scheduler WHERE next_time < ?', array(date('Y-m-d H:i:s')))->result_array();
        if (empty($result)) {
            echo date('Y-m-d H:i:s') . ": No task running\n";
        } else {
            echo date('Y-m-d H:i:s') . ": ".count($result)." tasks running\n";
            foreach ($result as $row) {
                switch ($row['type']) {
                    case 'sh':
                        $command = $row['command'];
                        break;
                    case 'ci':
                    default:
                        $command = 'php index.php ' . $row['command'];
                }

                $result = $retval = '';
                $cmd = 'nohup ' . $command . ' > "application/logs/' . $row['name'] . '-log-' . date('Y-m-d H:i:') . '00.php" &';
                exec($cmd, $result, $retval);

                $row['last_time'] = $row['next_time'];
                $row['next_time'] = $this->_model('task_scheduler')->get_next_time($row);

                if ($retval === 0) {
                    $row['last_success'] = $row['last_time'];
                } else {
                    $row['last_failed'] = $row['last_time'];
                }

                $this->_model('task_scheduler')->save($row, $row['id']);
            }
        }
        $stop = microtime();
        echo date('Y-m-d H:i:s') . ': Elapsed time: ' . ($stop - $start). "\n";
    }

}