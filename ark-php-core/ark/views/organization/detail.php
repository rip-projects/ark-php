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
    <div>
        <label><?php echo l('Address') ?></label>
        <span><?php echo $data['address'] ?></span>
    </div>
    <div>
        <label><?php echo l('Phone') ?></label>
        <span><?php echo $data['phone'] ?></span>
    </div>
    <div>
        <label><?php echo l('Email') ?></label>
        <span><?php echo $data['email'] ?></span>
    </div>
    <div>
        <label><?php echo l('Fax') ?></label>
        <span><?php echo $data['fax'] ?></span>
    </div>
    <div>
        <label><?php echo l('Website') ?></label>
        <span><?php echo $data['website'] ?></span>
    </div>
</fieldset>