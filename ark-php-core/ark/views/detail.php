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
    <?php foreach ($fields as $field): ?>
    <div>
        <label><?php echo humanize($field['name']) ?></label>
        <span><?php echo $data[$field['name']] ?></span>
    </div>
    <?php endforeach ?>
</fieldset>

