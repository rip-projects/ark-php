<?php

/**
 * MY_Config.php
 *
 * @package     ark-php
 * @author      chalid <chalid@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   chalid <chalid@xinix.co.id>
 *
 *
 */

class MY_Config extends CI_Config {

    function __construct() {
        parent::__construct();
        $this->_config_paths = array(APPPATH, ARCHPATH);
    }

}
