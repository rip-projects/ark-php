<?php $_POST[$field] = (is_array($_POST[$field])) ? $_POST[$field] : array($_POST[$field]) ?>
<div class="row-fluid <?php echo (empty($config['class'])) ? '' : $config['class'] ?>" style="margin: 0">
    <?php foreach ($options as $k => $v): ?>
    <label class="checkbox span6" style="font-weight: normal; margin: 0">
        <?php echo form_checkbox($field.'[]', $k, (!empty($_POST[$field])) ? in_array($k, $_POST[$field]) : '' ); ?> <?php echo $v ?>
    </label>
    <?php endforeach ?>
</div>