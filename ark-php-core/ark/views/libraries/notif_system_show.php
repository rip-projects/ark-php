<?php $uniqid = uniqid('notification-') ?>

<style type="text/css">
    #<?php echo $uniqid ?> .new-row-exists {
        padding: 2px 5px;
        background-color: #f00;
        text-transform: none;
        font-weight: bold;
        color: white;

        border-radius: 10px;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#f00), to(#e77));
        background-image: -webkit-linear-gradient(#f00, #e77);
        background-image: linear-gradient(#f00, #e77);

    }

    #<?php echo $uniqid ?> .message-row {
        padding: 5px 10px;
        border-bottom: 1px solid #ccc;
        width: 300px;
        display: block;
        overflow: hidden;
        cursor: pointer;
        text-decoration: none;
        position: relative;
        background-color: white;

    }

    #<?php echo $uniqid ?> a.message-row.new-message {
        background-color: #f4f4ff;
    }

    #<?php echo $uniqid ?> a.message-row:hover {
        background-color: #fafaff;
    }


    #<?php echo $uniqid ?> .message-image {
        width: 64px; height: 64px; border: 1px solid #ccc; display: block; float: left; margin-right: 5px;
        background-size: 64px 64px;
    }

    #<?php echo $uniqid ?> * {
        text-shadow: none;
        color: #333;
    }

    #<?php echo $uniqid ?> .message-message {
        display: block;
        height: 40px;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 220px;
    }

    #<?php echo $uniqid ?> .message-message p {
        padding: 0; margin: 0;
    }

    #<?php echo $uniqid ?> .message-time {
        position: absolute;
        right: 10px;
        bottom: 10px;
        font-size: 9px;
    }

    #<?php echo $uniqid ?> .see-all {
        display: none;
    }

    .notification-popup {
        bottom: 20px; top: auto; left: 20px; width: 300px;
        text-align: left;
    }
</style>

<li class="dropdown -notification" id="<?php echo $uniqid ?>">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <?php echo l('Notification') ?>&nbsp;<i class="status"> </i><i class="caret"></i>
    </a>
    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
        <li><center><img src="<?php echo theme_url('img/ajax-loader.gif') ?>"></center></li>
    </ul>
</li>

<script type="text/javascript">
    /**
    * @author Alexander Manzyuk <admsev@gmail.com>
    * Copyright (c) 2012 Alexander Manzyuk - released under MIT License
    * https://github.com/admsev/jquery-play-sound
    * Usage: $.playSound('http://example.org/sound.mp3');
    */
    // (function($){
    //     $(document).ready(function() {
    //         $('body').append('<span id="playSound"></span>');
    //     });
    //     $.extend({
    //         playSound: function(){
    //             $('#playSound').find('embed').remove();
    //             $('#playSound').append("<embed src='"+arguments[0]+"' hidden='true' autostart='true' loop='false'>");
    //         }
    //     });
    // })(jQuery);

    (function() {
        var timeout = null;
        var xhr = null;
        var $notif = $('#<?php echo $uniqid ?>');
        var $btn = $('#<?php echo $uniqid ?> a.dropdown-toggle');
        var $content = $('#<?php echo $uniqid ?> .dropdown-menu');
        var show = false;
        var lastNewestRow = localStorage['<?php echo $CI->auth->get_user_object()->id ?>:lastNewestRow'];

        $(document).live('click', function() {
            if (timeout) {
                clearTimeout(timeout);
                timeout = null;
            }
            if (xhr) {
                xhr.abort();
                xhr = null;
            }
            checkRowExists();
            return;
        });

        $('#<?php echo $uniqid ?> > a').live('click', function(evt) {
            evt.preventDefault();

            if (timeout) {
                clearTimeout(timeout);
                timeout = null;
            }
            if (xhr) {
                xhr.abort();
                xhr = null;
            }

            if (!$notif.hasClass('open')) {
                checkRowExists();
                return;
            }

            xhr = $.get('<?php echo site_url($self->system_fetch_uri) ?>.json', function(data) {
                console.log(data);
                $content.html(_.template($('#notification-message-row-template').html(), data));
                updateStatus(data);
                xhr = null;
            });
        });

        var updateStatus = function(data) {

            if (data && data.row_count && parseInt(data.row_count,10) > 0) {
                $btn.find('.status').html(data.row_count).addClass('new-row-exists');
            } else {
                $btn.find('.status').html('').removeClass('new-row-exists');
            }

            if (data.new_row && data.new_row.id != lastNewestRow) {
                localStorage[data.user_id + ':lastNewestRow'] = lastNewestRow = data.new_row.id;

                $('.notification-popup').remove();
                var $popup = $(_.template($('#notification-popup-template').html(), data));
                // $.playSound('<?php echo theme_url("sounds/bell.mp3") ?>');
                $('body').append($popup);
                setTimeout(function() {
                    $popup.fadeOut(function() {
                        $popup.remove();
                    });
                }, 3000);
            }
        }

        var checkRowExists = function() {
            xhr = $.get('<?php echo site_url($self->system_fetch_uri) ?>/last_accessed.json', function(data) {
                updateStatus(data);

                timeout = setTimeout(checkRowExists, <?php echo $self->system_timeout ?>);
                xhr = null;
            });
        }

        checkRowExists();

        // $(window).blur(function() {
        //     console.log('blur');
        //     if (xhr) {
        //         xhr.abort();
        //         xhr = null;
        //     }
        //     if (timeout) {
        //         clearTimeout(timeout);
        //         timeout = null;
        //     }
        // }).focus(function() {
        //     console.log('focus');
        //     if (!timeout) {
        //         timeout = setTimeout(checkRowExists, <?php echo $self->system_timeout ?>);
        //     }
        // });
    })();
</script>

<script type="text/template" id="notification-message-row-template">
    <!--<div class="message-row"><b>Notification</b></div>-->
    <% if (rows.length) { %>
    <% for (var i in rows) { %>
        <a class="message-row <%= (rows[i].is_new > 0) ? 'new-message' : '' %>" href="<%= rows[i].url || '#' %>">
            <i class="message-image" style="background-image: url(<%= rows[i].icon %>);"></i>
            <span class="message-message"><%= rows[i].message %></span>
            <span class="message-time"><%= rows[i].updated_time %> by <%= rows[i].created_fullname %></span>
            <div class="clearfix"></div>
        </a>
    <% } %>
    <% } else { %>
        <div><center>Empty</center></div>
    <% } %>
    <div class="see-all"><center><a href="<?php echo site_url($self->system_all_uri) ?>">See All</a></center></div>
</script>


<script type="text/template" id="notification-popup-template">
    <div class="alert alert-info notification-popup">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <h5><%= new_row.title %> <i style="font-size: 11px">(<%= new_row.updated_time %> by <%=new_row.created_fullname%>)</i></h5>
        <p><%= new_row.message %></p>
    </div>
</script>