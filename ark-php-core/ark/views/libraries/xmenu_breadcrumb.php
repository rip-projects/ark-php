<?php

/**
 * xmenu_breadcrumb.php
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

function _get_bc_uri($bc) {
    if (empty($bc['uri'])) {
        if (!empty($bc['children'])) {
            foreach ($bc['children'] as $child) {
                return _get_bc_uri($child);
            }
        } else {
            return '#';
        }
    }
    return $bc['uri'];
}
$count = count($breadcrumb);
$i = 0;
?>
<ul class="breadcrumb">
    <li><a href="<?php echo ($self->home_url) ? site_url($self->home_url) : base_url() ?>"><?php echo l('Home') ?></a></li>
    <?php foreach ($breadcrumb as $bc): ?>
        <?php $bc_uri = _get_bc_uri($bc) ?>
        <li <?php echo (++$i == $count) ? 'class="active"' : '' ?>><a href="<?php echo site_url($bc_uri) ?>"><?php echo l($bc['title']) ?></a></li>
    <?php endforeach ?>
</ul>