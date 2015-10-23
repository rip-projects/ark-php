<div class="header">
    <div class="pull-left">
        <?php echo $this->admin_panel->breadcrumb() ?>
    </div>
    <div class="pull-right">
        <?php echo xform_anchor($CI->_get_uri('compose'), 'Compose', 'class="btn"') ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="grid-top">
    <div class="pull-left">
        <?php echo xform_anchor($CI->_get_uri('trash/inbox'), 'Trash', 'class="btn btn-danger mass-action"') ?>
    </div>
    <div class="pull-right">
        <?php echo xview_filter($filter) ?>
    </div>
    <div class="clearfix"></div>
</div>

<?php echo $this->user_inbox->show($messages) ?>

<div class="grid-bottom">
    <div class="pull-left">
        <?php echo $this->pagination->per_page_changer() ?>
    </div>
    <div class="pull-right">
        <?php echo $this->pagination->create_links() ?>
    </div>
    <div class="clearfix"></div>
</div>
