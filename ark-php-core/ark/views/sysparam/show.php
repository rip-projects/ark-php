<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(l(humanize(get_class($CI))))) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label><?php echo l('Group') ?></label>
            <input type="text" value="<?php echo set_value('sgroup') ?>" name="sgroup" autofocus placeholder="<?php echo l('Group') ?>" />
        </div>
        <div>
            <label><?php echo l('Key') ?></label>
            <input type="text" value="<?php echo set_value('skey') ?>" name="skey" placeholder="<?php echo l('Key') ?>" />
        </div>
        <div>
            <label><?php echo l('Short Value') ?></label>
            <input type="text" value="<?php echo set_value('svalue') ?>" name="svalue" placeholder="<?php echo l('Short Value') ?>" />
        </div>
        <div>
            <label><?php echo l('Long Value') ?></label>
            <textarea name="lvalue" cols="30" rows="10"><?php echo set_value('lvalue') ?></textarea>
        </div>
        <div>
            <label><?php echo l('Is default') ?></label>
            <?php echo xform_boolean('is_default', set_value('is_default')) ?>
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>