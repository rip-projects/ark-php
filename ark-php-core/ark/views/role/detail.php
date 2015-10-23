<?php $title = humanize(get_class($CI)). ' Detail' ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clearfix"></div>

<fieldset>
    <legend><?php echo $title ?></legend>
    <div>
        <label><?php echo l('Name') ?></label>
        <span><?php echo $data['name'] ?></span>
    </div>
    <?php if ($CI->auth->get_user_object()->is_top_member && !empty($data['organization_id'])) : ?>
        <div>
            <label>Organization</label>
            <span><?php echo format_model_param($data['organization_id'], 'organization') ?></span>
        </div>
    <?php endif ?>
</fieldset>

<?php if (!empty($CI->uri->rsegments[3])): ?>
<div style="margin-top: 10px;">
    <form action="<?php echo site_url('role/add_privilege/' . $CI->uri->rsegments[3]) ?>" method="post" class="ajaxform">
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
