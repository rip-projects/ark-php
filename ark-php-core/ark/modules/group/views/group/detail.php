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
</fieldset>

<?php if (!empty($CI->uri->rsegments[3])): ?>
<div style="margin-top: 10px;">
    <form action="<?php echo site_url('group/add_organization/' . $CI->uri->rsegments[3]) ?>" method="post" class="ajaxform">
        <fieldset>
            <legend><?php echo l('Group Data') ?></legend>
            <div>
                <?php echo form_dropdown('org_id', $org_options) ?>
                <input type="submit" value="<?php echo l('Add') ?>" />
            </div>
        </fieldset>
    </form>
    <?php echo $this->grid_organization->show($organizations); ?>
</div>
<?php endif ?>

