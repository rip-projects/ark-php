<?php

/**
 * ximage.php
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

$config = array(
    'presets' => array(
        'thumb' => array(
            'width' => 50,
            'height' => 80,
        ),

        'small' => array(
            'width' => 100,
            'height' => 150,
        ),
        'normal' => array(
            'width' => 300,
            'height' => 400,
        ),
        'large' => array(
            'width' => 600,
            'height' => 600
        ),
    ),
    'valid_required' => true,
    'default_image' => 'default/default.gif',
    'encrypt_name' => true,
);