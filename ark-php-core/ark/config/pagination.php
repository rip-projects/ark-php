<?php

/**
 * pagination.php
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

$config = array(
        'per_page' => 10,
        'uri_segment' => 3,
        'full_tag_open' => '<ul>',
        'full_tag_close' => '</ul>',
        'first_tag_open' => '<li class="first">',
        'first_tag_close' => '</li>',
        'prev_tag_open' => '<li class="prev">',
        'prev_tag_close' => '</li>',
        'next_tag_open' => '<li class="next">',
        'next_tag_close' => '</li>',
        'last_tag_open' => '<li class="last">',
        'last_tag_close' => '</li>',
        'cur_tag_open' => '<li class="selected active"><a href="#">',
        'cur_tag_close' => '</a></li>',
        'num_tag_open' => '<li>',
        'num_tag_close' => '</li>',
        'first_link' => '<<',
        'last_link' => '>>',
        // 'display_pages' => true,
        'per_pages' => array( 10, 25, 50, 100),
        'per_page_changer_prefix' => 'Show ',
);
