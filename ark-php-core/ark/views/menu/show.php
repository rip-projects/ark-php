<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(l(humanize(get_class($CI))))) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri($CI->uri->rsegments[2]), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>

<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label class="mandatory">Title</label>
            <input type="text" value="<?php echo set_value('title') ?>" name="title" class="medium" placeholder="Menu title" />
        </div>
        <div>
            <label>URI</label>
            <input type="text" value="<?php echo set_value('uri') ?>" name="uri" placeholder="CodeIgnition URI" />
        </div>
        <div>
            <label>Position</label>
            <input type="text" value="<?php echo set_value('position') ?>" name="position" class="small" placeholder="Number" />
        </div>
        <div>
            <label>Parent</label>
            <?php echo form_dropdown('parent_id', $parent_options) ?>
            <div class="clearfix"></div>
        </div>
        <?php if (!empty($id)): ?>
            <div>
                <label><?php echo l('Status') ?></label>
                <?php echo xform_boolean('status') ?>
                <div class="clearfix"></div>
            </div>
        <?php endif ?>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>