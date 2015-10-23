<?php

/**
 * pagination_per_page_changer.php
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

?>

<div class="page-changer pagination">
    <div class="pull-left">
        <span class="pull-left" style="padding-right: 5px">
            <?php echo l($self->per_page_changer_prefix) ?>
        </span>
        <ul>
            <?php foreach ($self->per_pages as $per_page): ?>
                <li <?php echo ($current_per_page == $per_page) ? 'class="selected active"' : '' ?>>
                    <a href="<?php echo $self->base_url ?>?per_page=<?php echo $per_page ?>"><?php echo $per_page ?></a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
</div>