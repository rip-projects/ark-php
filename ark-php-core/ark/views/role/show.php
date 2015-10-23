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
            <label class="mandatory"><?php echo l('Name') ?></label>
            <input type="text" value="<?php echo set_value('name') ?>" name="name" placeholder="<?php echo l('Role name') ?>" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Is main') ?></label>
            <?php echo xform_boolean('is_main') ?>
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>

<?php if (!empty($id)): ?>
<div style="margin-top: 10px;">
    <form action="<?php echo site_url('role/add_privilege/' . $id) ?>" method="post" class="ajaxform">
        <fieldset>
            <legend><?php echo l('Privileges') ?></legend>
            <div>
                <input type="text" name="uri" />
                <input type="submit" value="<?php echo l('Add') ?>" />
            </div>
        </fieldset>
    </form>
    <?php echo $this->grid_privilege->show($privileges); ?>
</div>
<?php endif ?>