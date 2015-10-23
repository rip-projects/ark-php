<style type="text/css">
    .grid { border: 1px solid #BBF; }
    .field_field { min-width:210px!important; }
    #field_grid td input.text,
    #field_grid td select { width: 100%; }
</style>

<script type="text/javascript">
    function addField() {
        var $row = $($('#template').html());
        $('#field_grid tbody').append($row);
        xn.helper.stylize($row);
        // $('#field_grid tr:last').clone().appendTo('#field_grid tbody');

        $('#field_grid').find("tr:nth-child(odd)").removeClass('even').removeClass('odd').addClass("odd");
        $('#field_grid').find("tr:nth-child(even)").removeClass('even').removeClass('odd').addClass("even");
    }

    function removeField($o) {
        if ($o.parents('tbody').find('tr').length <= 2) {
            addField();
        }
        $o.parents('tr').remove();
    }

    $('#add_field').live('click', function(evt) {
        addField();
        return evt.preventDefault(); 
    });

    $('.btn-remove').live('click', function(evt) {
        removeField($(this));
        return evt.preventDefault();
    });

    $('.btn-up').live('click', function(evt) {
        var current = $(this).parents('tr');
        current.prev().before(current);
        return evt.preventDefault();
    });

    $('.btn-down').live('click', function(evt) {
        var current = $(this).parents('tr');
        current.next().after(current);
        return evt.preventDefault();
    });
</script>

<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(l(humanize(get_class($CI))))) ?>

<?php echo $this->admin_panel->breadcrumb() ?>
<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label class="mandatory"><?php echo l('Name') ?></label>
            <input type="text" value="<?php echo set_value('name') ?>" name="name" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Controller Base Class') ?></label>
            <input type="text" value="<?php echo set_value('controller_base_class') ?>" name="controller_base_class" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Model Base Class') ?></label>
            <input type="text" value="<?php echo set_value('model_base_class') ?>" name="model_base_class" />
        </div>
        <div>
            <label><?php echo l('Create Table') ?></label>
            <?php echo form_checkbox('create_table', 'true', true) ?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?php echo l('Fields') ?></legend>
        <table class="grid table table-hover table-striped table-condensed" id="field_grid">
            <tr>
                <th><?php echo l('Name') ?></th>
                <th><?php echo l('Type') ?></th>
                <th><?php echo l('Size') ?></th>
                <th><?php echo l('Extra') ?></th>
                <th>&nbsp;</th>
            </tr>
            <?php if (!empty($_POST['fields'])): ?>
                <?php foreach ($_POST['fields'] as $i => $field): ?>
                    <tr>
                        <td>
                            <input type="text" value="<?php echo $field ?>" name="fields[]" class="field_field"/>
                        </td>
                        <td>
                            <?php echo form_dropdown('types[]', $type_options, $_POST['types'][$i], 'class="field_field"') ?>
                        </td>
                        <td>
                            <input type="text" value="<?php echo $_POST['sizes'][$i] ?>" name="sizes[]" class="field_field"/>
                        </td>
                        <td>
                            <input type="text" value="<?php echo $_POST['extras'][$i] ?>" name="extras[]" class="field_field"/>
                        </td>
                        <td>

                            <a href="#" class="btn-remove">Delete</a>
                            <a href="#" class="btn-up">Up</a>
                            <a href="#" class="btn-down">Down</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td>
                        <input type="text" value="" name="fields[]" class="field_field"/>
                    </td>
                    <td>
                        <?php echo form_dropdown('types[]', $type_options, '', 'class="field_field"') ?>
                    </td>
                    <td>
                        <input type="text" value="" name="sizes[]" class="field_field"/>
                    </td>
                    <td>
                        <input type="text" value="" name="extras[]" class="field_field"/>
                    </td>
                    <td>
                        <a href="#" class="btn-remove">Delete</a>
                        <a href="#" class="btn-up">Up</a>
                        <a href="#" class="btn-down">Down</a>
                    </td>
                </tr>
            <?php endif ?>
        </table>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        or
        <a href="#" class="btn" id="add_field">Add Field</a>
    </div>
</form>

<script type="text/template" id="template">
    <tr>
        <td>
            <input type="text" value="" name="fields[]" class="field_field"/>
        </td>
        <td>
            <?php echo form_dropdown('types[]', $type_options, '', 'class="field_field"') ?>
        </td>
        <td>
            <input type="text" value="" name="sizes[]" class="field_field"/>
        </td>
        <td>
            <input type="text" value="" name="extras[]" class="field_field"/>
        </td>
        <td>
            <a href="#" class="btn-remove">Delete</a>
            <a href="#" class="btn-up">Up</a>
            <a href="#" class="btn-down">Down</a>
        </td>
    </tr>
</script>