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
        <?php foreach ($fields as $field): ?>
            <div>
                <label><?php echo humanize($field['name']) ?></label>
                <?php if ($field['type'] == 'LONG'): ?>
                    <input type="text" value="<?php echo set_value($field['name']) ?>" name="<?php echo $field['name'] ?>" class="number" <?php if (empty($next)) { $next = true; echo ' autofocus '; } ?> placeholder="<?php echo humanize($field['name']) ?> (Number)" />
                <?php elseif ($field['type'] == 'DATETIME'): ?>
                    <?php echo xform_date($field['name']) ?>
                <?php elseif ($field['type'] == 'BLOB'): ?>
                    <textarea name="<?php echo $field['name'] ?>" cols="30" rows="10"><?php echo set_value($field['name']) ?></textarea>
                <?php else: ?>
                    <input type="text" value="<?php echo set_value($field['name']) ?>" name="<?php echo $field['name'] ?>" <?php if (empty($next)) { $next = true; echo ' autofocus '; } ?> placeholder="<?php echo humanize($field['name']) ?>" />
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>