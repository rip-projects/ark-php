<?php $title = 'Edit ' . humanize(get_class($CI)) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => current_url(), 'title' => 'Register'),
))
?>

<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post">
    <fieldset>
        <?php if (!empty($_REQUEST['act'])): ?>
            <input type="hidden" name="act" value="<?php echo $_REQUEST['act'] ?>" />
        <?php endif ?>
        <div>
            <label class="mandatory"><?php echo l('First Name') ?></label>
            <input type="text" value="<?php echo set_value('first_name') ?>" name="first_name" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Last Name') ?></label>
            <input type="text" value="<?php echo set_value('last_name') ?>" name="last_name" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Email') ?></label>
            <input type="text" value="<?php echo set_value('email') ?>" name="email" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Username') ?></label>
            <input type="text" value="<?php echo set_value('username') ?>" name="username" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Password') ?></label>
            <input type="password" value="" name="password" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Retype Password') ?></label>
            <input type="password" value="" name="password2" />
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" value="Save" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>