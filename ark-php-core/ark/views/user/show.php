<?php if (empty($id)): ?>
<script type="text/javascript">
    $(function() {
        $('input[name=email], input[name=first_name], input[name=last_name]').blur(function() {
            var $username = $('input[name=username]');
            var $email = $('input[name=email]');

            if ($email.val() != '') {
                $username.val($email.val().trim().toLowerCase().split('@')[0]);
            } else {
                var $fname = $('input[name=first_name]');
                var $lname = $('input[name=last_name]');

                var names = [];
                if ($fname.val() != '') {
                    names.push($fname.val().toLowerCase());
                }
                if ($lname.val() != '') {
                    names.push($lname.val().toLowerCase());
                }
                $username.val(names.join('.'));
            }

        });

        $('#btn-generate-password').click(function(evt) {
            var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
            var string_length = 8;
            var randomstring = '';
            for (var i=0; i<string_length; i++) {
                var rnum = Math.floor(Math.random() * chars.length);
                randomstring += chars.substring(rnum,rnum+1);
            }
            $('input[type=password]').val(randomstring);
            return evt.preventDefault();
        });
    });
</script>
<?php endif ?>
<?php $title = l( (empty($id) ? 'Add %s' : 'Edit %s'), array(humanize(get_class($CI))) ) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label class="mandatory"><?php echo l('Email') ?></label>
            <input type="text" value="<?php echo set_value('email') ?>" name="email" placeholder="<?php echo l('Email') ?>" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('First Name') ?></label>
            <input type="text" value="<?php echo set_value('first_name') ?>" name="first_name"  placeholder="<?php echo l('First name') ?>" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Last Name') ?></label>
            <input type="text" value="<?php echo set_value('last_name') ?>" name="last_name"  placeholder="<?php echo l('Last name') ?>" />
        </div>
        <div>
            <label class="mandatory"><?php echo l('Username') ?></label>
            <input type="text" value="<?php echo set_value('username') ?>" name="username"  placeholder="<?php echo l('Username (Character and number only)') ?>" />
        </div>
        <div>
            <label class="mandatory span2"><?php echo l('Password') ?></label>
            <div class="input-append span10">
                <input type="password" value="" name="password"  placeholder="<?php echo l('Password') ?>" />
                <input type="button" value="<?php echo l('Generate Password') ?>" id="btn-generate-password" style="margin-left:0" />
            </div>
        </div>
        <div>
            <label class="mandatory"><?php echo l('Retype Password') ?></label>
            <input type="password" value="" name="password2"  placeholder="<?php echo l('Password') ?>" />
        </div>
        <div>
            <label><?php echo l('Gender') ?></label>
            <?php echo xform_lookup('gender') ?>
        </div>
        <div>
            <label><?php echo l('Phone') ?></label>
            <input type="text" value="<?php echo set_value('phone') ?>" name="phone" placeholder="Phone Number">
        </div>
        <div>
            <label><?php echo l('Yahoo ID') ?></label>
            <input type="text" value="<?php echo set_value('yahoo_id') ?>" name="yahoo_id" placeholder="Yahoo ID">
        </div>
        <div>
            <label><?php echo l('Google ID') ?></label>
            <input type="text" value="<?php echo set_value('google_id') ?>" name="google_id" placeholder="Google ID">
        </div>
        <div>
            <label><?php echo l('Roles') ?></label>
            <?php echo form_multiselect('roles[]', $role_items, @$_POST['roles']) ?>
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>