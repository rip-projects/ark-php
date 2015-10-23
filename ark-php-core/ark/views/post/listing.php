<div class="header">
    <div class="pull-left">
        <?php echo $this->admin_panel->breadcrumb() ?>
    </div>
    <div class="pull-right">
        <?php //echo xform_anchor($CI->_get_uri('import') . '/csv', 'Import', 'class="btn"') ?>
        <?php echo xform_anchor($CI->_get_uri('add'), 'Add', 'class="btn"') ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="grid-top">
    <div class="pull-left">
        <?php echo xform_anchor($CI->_get_uri('delete'), 'Delete', 'class="btn btn-danger mass-action"') ?>
    </div>
    <div class="pull-right">
        <?php
        $arr = (empty($filter['tag'])) ? array() : array($filter['tag']);
        $extra = form_dropdown('tag', $tag_options, $arr, 'id="tag-select"');
        $extra .=
                "<script>
                        $(function() {
                            $('#tag-select').change(function() {
                                $(this).parents('form').submit();
                            });
                        });
                        </script>";
        ?>
        <?php echo xview_filter($filter, $extra) ?>
    </div>
    <div class="clearfix"></div>
</div>

<?php echo $this->listing_grid->show($data['items']) ?>

<?php if (!$this->input->is_ajax_request()): ?>
    <div class="grid-bottom">
        <div class="pull-left">
            <?php echo $this->pagination->per_page_changer() ?>
        </div>
        <div class="pull-right">
            <?php echo $this->pagination->create_links() ?>
        </div>
        <div class="clearfix"></div>
    </div>
<?php endif ?>