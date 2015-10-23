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

        $('#btn-logout').click(function(evt) {
            location.href = $(this).attr('href');
            return evt.preventDefault();
        });
    });
</script>

<div id="login-pane" class="login-pane">
    <div>
        <form action="" method="post">
            <div class="login-form">

                <div class="logo">
                    <div class="title">Error?!<br /><strong>No!</strong></div>
                </div>

                <div class="system-time">
                    <span class="xinix-date"></span> &#149; <span class="xinix-time"></span>
                </div>
                <div style="text-align: center">
                    <p>
                        Something <span style="color:green">happened</span>?! Please contact system administrator. and tell him correctly what you've been through and <span style="color: red">please</span> stop crying.
                    </p>
                    <?php if (!empty($_GET['msg'])): ?>
                    <p>
                        <span style="color:blue">
                            System tell you that the error is:
                        </span>
                        <br/>
                        <?php echo $_GET['msg']?>
                    </p>
                    <?php endif ?>
                </div>
                <div style="text-align: center">
                    <a href="<?php echo site_url('user/logout') ?>" class="btn" id="btn-logout">Logout</a>
                </div>
            </div>
        </form>
    </div>
</div>