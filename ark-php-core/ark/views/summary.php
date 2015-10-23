<fieldset>
    <legend><?php echo l('Summary') ?></legend>
    <?php foreach($data as $fieldname => $value): ?>
    <div>
        <label><?php echo l(humanize($fieldname)) ?></label>
        <span><?php echo $value ?></span>
    </div>
    <?php endforeach ?>
</fieldset>