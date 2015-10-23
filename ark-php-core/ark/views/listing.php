<div class="header">
    <div class="pull-left">
        <?php
        // FIXME should be plugin area
        // echo $this->admin_panel->breadcrumb() ?>
    </div>
    <div class="pull-right btn-group">
        <?php echo xform_anchor($CI->_get_uri('add'), l('Add'), 'class="btn"') ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="grid-top">
    <div class="pull-left btn-group">
        <?php echo xform_anchor($CI->_get_uri('trash'), l('Trash'), 'class="btn btn-danger mass-action"') ?>
        <?php if (isset($CI->CAN_DELETE) && $CI->CAN_DELETE): ?>
        <?php echo xform_anchor($CI->_get_uri('delete'), l('Delete'), 'class="btn btn-danger mass-action"') ?>
        <?php endif ?>
    </div>
    <div class="pull-right">
        <?php if (isset($CI->CAN_SEARCH) && $CI->CAN_SEARCH): ?>
        <?php echo xview_filter($filter) ?>
        <?php endif ?>
    </div>
    <div class="clearfix"></div>
</div>

<?php echo $this->listing_grid->show($data['items']) ?>

<?php if (!$this->input->is_ajax_request()): ?>
    <div class="row-fluid grid-bottom">
        <div class="span6 left">
            <?php echo $this->pagination->per_page_changer() ?>
        </div>
        <div class="span6 right">
            <?php echo $this->pagination->create_links() ?>
        </div>
    </div>
<?php endif ?>
