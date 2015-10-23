<div class="header">
    <div class="pull-left">
        <?php echo $this->breadcrumb->show() ?>
    </div>
    <div class="clearfix"></div>
</div>

<fieldset>
    <div>
        <label><?php echo l('Module Count') ?></label>
        <span><?php echo $module_count ?></span>
    </div>
    <div>
        <label><?php echo l('Method Count') ?></label>
        <span><?php echo $method_count ?></span>
    </div>
</fieldset>

<div class="grid-container table-bordered">
    <table class="grid table table-hover table-striped table-condensed">
        <tr>
            <th><?php echo l('Name') ?></th>
            <th><?php echo l('Base Directory') ?></th>
            <th><?php echo l('Methods') ?></th>
        </tr>
        <?php foreach ($modules as $module): ?>
            <tr>
                <td style="vertical-align: top"><?php echo $module['name'] ?></td>
                <td style="vertical-align: top"><?php echo $module['base_dir'] ?></td>
                <td style="vertical-align: top">
                    <?php if (!empty($module['methods'])): ?>
                        <ul>
                            <?php foreach ($module['methods'] as $method): ?>
                                <li><?php echo $method ?></li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>

<br />