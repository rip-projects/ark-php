<!DOCTYPE html>
<html>
<head>
    <title><?php echo $CI->_page_title; ?></title>
    <!-- Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo theme_url('css/global.css') ?>" rel="stylesheet" media="screen" />
    <link href="<?php echo theme_url('css/bootstrap.css') ?>" rel="stylesheet" media="screen" />
    <link href="<?php echo theme_url('css/bootstrap-responsive.min.css') ?>" rel="stylesheet" />
    <link href="<?php echo theme_url('css/user.css') ?>" rel="stylesheet" media="screen" />
    <link href="<?php echo theme_url('js/code-prettify/prettify.css') ?>" media="all" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="<?php echo theme_url('js/jquery-1.8.2.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/underscore-min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/bootstrap.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/code-prettify/prettify.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/datepicker.js') ?>"></script>
</head>

<?php $USER = $CI->auth->get_user() ?>

<body>

    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a class="brand" href="<?php echo base_url() ?>" style="width:100px">
                    &nbsp;
                    <?php // echo $CI->config->item('page_title') ?>
                </a>
                <div class="nav-collapse">
                    <?php
                    // FIXME should be plugin area
                    //echo $this->admin_panel->show() ?>


                    <ul class="menu nav pull-right">
                        <?php if (!empty($USER)): ?>
                        <li><?php // echo $this->notif->system->show() ?></li>
                        <?php endif ?>
                        <!-- <li><span class="system-time"><span class="xinix-date"></span> &#149; <span class="xinix-time"></span></span></li> -->
                        <?php if (!empty($USER)): ?>
                        <li class="user-sys has-children dropdown">
                            <a href="<?php echo site_url("profile"); ?>" data-toggle="dropdown">
                                <?php echo $USER['first_name'] . ' ' . $USER['last_name'] ?>
                                <i class="caret"></i>
                            </a>
                            <ul class="menu dropdown-menu">
                                <li><a href="<?php echo site_url("profile"); ?>"><?php echo l('Profile Page') ?></a></li>
                                <li><a href="<?php echo $CI->auth->change_password_page() ?>"><?php echo l('Change Password') ?></a></li>
                                <li><a href="<?php echo site_url("user/logout"); ?>"><?php echo l('Logout') ?></a></li>
                            </ul>
                        </li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="wrap">
        <div class="container-fluid" id="container">
            <?php echo xview_error() ?>
            <?php echo xview_info() ?>
            <?php echo $this->load->view($CI->_view, $CI->_data, true) ?>
        </div>
        <div id="push"></div>
    </div>

    <div id="footer">
        <p class="muted credit">
            <span class="big">
            Copyright &copy; 2012 <a href="http://xinix.co.id" target="blank">PT Sagara Xinix Solusitama</a>. All rights reserved.
            </span>
            <span class="tiny">
                <a href="http://xinix.co.id" target="blank">PT Sagara Xinix Solusitama</a>
            </span>
            <?php if ($CI->config->item('enable_profiler')): ?>
            <span class="profiler">
                <a href="#" id="profiler_btn">
                    <span class="big">
                        ( time: {elapsed_time}, mem: {memory_usage} )
                    </span>
                    <span class="tiny">
                        (P)
                    </span>
                </a>
            </span>
            <?php endif ?>
        </p>
    </div>

    <script type="text/template" id="template-no-data-selected-error">
        <p>No selected record.</p>
    </script>

    <script type="text/template" id="template-modal">
        <div class="modal hide fade">
            <div class="modal-header btn-inverse">
                <button type="button" class="close btn-inverse" data-dismiss="modal" aria-hidden="true">x</button>
                <% if (title) { %>
                <h3><%= title %></h3>
                <% } %>
            </div>
            <div class="modal-body">
                <%= body || '' %>
            </div>
            <% if (footer) { %>
            <div class="modal-footer">
                <%= footer %>
            </div>
            <% } %>
        </div>
    </script>

    <script src="<?php echo theme_url('js/xn.js') ?>" data-base-url="<?php echo site_url('/') ?>"></script>
</body>
</html>
