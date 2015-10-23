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

<div id="login-pane" class="login-pane">
    <div>
        <form action="" method="post">
            <div class="login-form">

                <div class="logo">
                    <div class="title">Unauthorized<br /><strong>Xinix</strong></div>
                </div>

                <div class="system-time">
                    <span class="xinix-date"></span> &#149; <span class="xinix-time"></span>
                </div>
                <div style="text-align: center">
                    <p>
                        <?php echo l('You are <span style="color:red">unauthorized</span> to see this page.') ?>
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
                	<p>
	                    <a href="<?php echo $CI->auth->login_page() ?>" class="btn"><?php echo l('Login') ?></a>
						<a href="<?php echo base_url() ?>" class="btn"><?php echo l('Home') ?></a>
                	</p>
                </div>
            </div>
        </form>
    </div>
</div>
