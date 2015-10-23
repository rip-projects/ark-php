<script type="text/javascript">
    $(function() {
        function resize() {
            $('#login-pane').css({
                left: ($(window).width() - $('#login-pane').width()) / 2,
                top: (($(window).height() - $('#login-pane').height()) / 2) - 25
            });
        }

        $(window).resize(function() {
            resize();
        });
        resize();
    });
</script>

<div id="login-pane" class="login-pane<?php echo (is_error_exists()) ? " accessdenied" : '' ?>">
    <div>
        <form action="" method="post">
            <div class="login-form">

                <?php /* Put your logo here inside div.logo */ ?>
                <div class="logo">
                    <div class="title">Xinix<br /><strong>Arch PHP</strong></div>
                </div>

                <div class="system-time">
                    <span class="xinix-date"></span> &#149; <span class="xinix-time"></span>
                </div>
                <?php if (!$CI->config->item('use_db')): ?>
                <div style="text-align: center; color: red; font-weight: bold">
                    Database not ready!
                </div>
                <?php endif ?>
                <div>
                    <input type="text" name="login" value=""  placeholder="<?php echo l('Username/Email') ?>" />
                </div>
                <div>
                    <input type="password" name="password" value="" placeholder="<?php echo l('Password') ?>" />
                </div>
                <div style="padding-top:10px">
                    <input type="hidden" name="continue" value="" />
                    <input type="submit" value="Login" />
                </div>
            </div>
        </form>
    </div>
</div>