<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1" />
        <link type="image/x-icon" href="<?php echo theme_url('../favicon.ico') ?>" rel="Shortcut icon" />
        <link type="image/gif" href="<?php echo theme_url('../favicon.gif') ?>" rel="Shortcut icon" />

        <link href="<?php echo theme_url('css/fonts/stylesheet.css') ?>" media="all" rel="stylesheet" type="text/css" />
        <link href="<?php echo theme_url('css/plugins.css') ?>" media="all" rel="stylesheet" type="text/css" />
        <link href="<?php echo theme_url('css/keyframe.css') ?>" media="all" rel="stylesheet" type="text/css" />
        <link href="<?php echo theme_url('css/global.css') ?>" media="all" rel="stylesheet" type="text/css" />
        <link href="<?php echo theme_url('css/user.css') ?>" media="all" rel="stylesheet" type="text/css" />
        <link href="<?php echo theme_url('css/lionbars.css') ?>" media="all" rel="stylesheet" type="text/css" />
        <link href="<?php echo theme_url('js/code-prettify/prettify.css') ?>" media="all" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="<?php echo theme_url('js/jquery.tools.min.js') ?>"></script>
        <script type='text/javascript' src="<?php echo theme_url('js/jquery.bgiframe.min.js') ?>"></script>
        <script type='text/javascript' src="<?php echo theme_url('js/jquery.ajaxQueue.js') ?>"></script>
        <script type='text/javascript' src="<?php echo theme_url('js/jquery.printElement.js') ?>"></script>
        <script type='text/javascript' src="<?php echo theme_url('js/jquery.autocomplete.js') ?>"></script>
        <script type="text/javascript" src="<?php echo theme_url('js/jquery.contextMenu.js') ?>"></script>
        <script type='text/javascript' src="<?php echo theme_url('js/jquery.easing.1.3.js') ?>"></script>
        <script type='text/javascript' src="<?php echo theme_url('js/jquery.lionbars.0.3.js') ?>"></script>
        <script type='text/javascript' src="<?php echo theme_url('js/code-prettify/prettify.js') ?>"></script>

        <script type="text/javascript" src="<?php echo theme_url('js/xn.js') ?>" data-xn-config="baseUrl: '<?php echo base_url() ?>'"></script>
        <script type="text/javascript" src="<?php echo theme_url('js/xn-global-main.js') ?>"></script>
        
        <title><?php echo $CI->_page_title; ?></title>
    </head>

    <?php $USER = $CI->auth->get_user() ?>

    <body>
        <div id="layout" class="layout <?php echo (!$USER['is_login']) ? "login" : "" ?>">
            <div class="logo-background">
                <?php if ($USER['is_login']): ?>
                    <div id="layout-header" class="layout-header">
                        <div class="wrapper">
                            <div class="top-nav"><?php echo $this->admin_panel->show() ?></div>
                            <div class="top-nav top-nav-right right">
                                <ul class="menu">
                                    <li class="status"><span class="system-time"><span class="xinix-date"></span> &#149; <span class="xinix-time"></span></span></li>
                                    <li class="user-sys">
                                        <a href="<?php echo site_url("profile"); ?>"><?php echo $USER['first_name'] . ' ' . $USER['last_name'] ?></a>
                                        <ul>
                                            <li><a href="<?php echo site_url("profile"); ?>"><?php echo l('Profile Page') ?></a></li>
                                            <li><a href="<?php echo site_url("user/change_password/" . $USER['id']); ?>"><?php echo l('Change Password') ?></a></li>
                                            <li><a href="<?php echo site_url("user/logout"); ?>"><?php echo l('Logout') ?></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <div id="layout-body" class="layout-body">
                    <div class="wrapper">
                        <div id="layout-content" class="the-content">
                            <?php echo xview_error() ?>
                            <?php echo xview_info() ?>
                            <div class="clearfix"></div>
                            <?php echo $this->load->view($CI->_view, $CI->_data, true) ?>
                        </div>
                    </div>
                </div>

                <div id="layout-footer" class="layout-footer">
                    <div class="wrapper">
                        <div id="footer-copyright" class="footer-copyright">
                            <p>
                                Copyright &copy; 2011 <a href="http://xinix.co.id" target="blank">Xinix Technology</a>. All rights reserved. 
                                <?php if ($CI->config->item('enable_profiler')): ?>
                                    <a href="#" id="profiler_btn">( time: {elapsed_time}, mem: {memory_usage} )</a>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>