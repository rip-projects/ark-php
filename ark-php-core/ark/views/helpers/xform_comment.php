<style>
    #commentor { margin-top: 10px; display: none; }
    #commentor label {
        float: left;
        display: block;
        width: 100px;
    }
    #commentor > img { float: left; width: 48px; height: 48px; margin-right: 10px;}
    #commentor > div#commentor-data { float: left;}
    div#commentor-data div { margin: 0 0 10px 0; }

    #commentor span.text {
        border: 1px solid #f5bf79;
        display: table;
        width: 500px;
        font-size: 1.2em;
        padding: 5px 10px;
    }
</style>

<script type="text/javascript">
    $.popup = function(options) {
        options = $.extend({
            'url': '#',
            'width': 600,
            'height': 500,
            'callback': null
        }, options);
        
        newwindow=window.open(options.url,'name','height=' + options.height + ',width=' + options.width);
        if (window.focus) {newwindow.focus()}
        return false;
    }
    
    function callback_auth(data) {
        $('#commentor').fadeIn();
        $('#commentor img').attr('src', data.picture);
        $('#commentor #comment-name').html(data.name + '&nbsp;');
        $('#commentor #comment-name-hidden').val(data.name);
        $('#commentor #comment-email').html(data.email+ '&nbsp;');
        $('#commentor #comment-email-hidden').val(data.email);
        $('#commentor #comment-url').html(data.url+ '&nbsp;');
        $('#commentor #comment-url-hidden').val(data.url);
        $('#commentor #comment-picture-hidden').val(data.picture);
        
        $('.comment-via li').removeClass('active');
        $('.comment-via li.' + data.via).addClass('active');
    }
    
    function loadComments() {
        var URL = "<?php echo site_url('user/show_video_comment/'.$video_id) ?>";
        $('#comment-container').load(URL)
    }

    $(function() {
        $('.comment-via .facebook a').click(function(evt) {
            $.popup({
                'url': xn.helper.createUrl('site/auth/facebook'),
                'callback': 'callback_auth'
            }); 
            return evt.preventDefault();
        })
        
        $('.comment-via .twitter a').click(function(evt) {
            $.popup({
                'url': xn.helper.createUrl('site/auth/twitter'),
                'callback': 'callback_auth'
            }); 
            return evt.preventDefault();
        })
        
        $('form.ajax').ajaxForm ({
            dataType: 'json',
            success: function (data) {
                            
                if(typeof(data.error) !== 'undefined') {
                    $('#message').html('<div class="error">' + data.error.message + '</div>');
                } else {
                    $('#message').html('<div class="info">Komentar anda telah ditambahkan</div>');
                }
                loadComments();
                $('#commentor').fadeOut();
                $('textarea').val('');
                $('.comment-via li').removeClass('active');
                
            },
            error: function() {
                $('#message').html('<div class="error">Internal Server Error</div>');
                loadComments();
                $('#commentor').fadeOut();
                $('textarea').val('');
                $('.comment-via li').removeClass('active');
            }
        });
        
        loadComments();
    });
</script>

<div class="comment-form">
    <form class="ajax" action="<?php echo site_url('user/video_comment/' . $video_id) ?>" method="post">

        <h2>Add New Comments</h2>

        <div id="message"></div>

        <div>
            <ul class="comment-via">
                <li class="facebook"><a href="#facebook">facebook</a></li>
                <li class="twitter"><a href="#twitter">twitter</a></li>
            </ul>

            <div id="commentor">
                <img src=""/>
                <input type="hidden" name="picture" value="" id="comment-picture-hidden"/>
                <div id="commentor-data">
                    <div>
                        <label>Nama</label>
                        <span id="comment-name" class="text">&nbsp;</span>
                        <input type="hidden" name="name" value="" id="comment-name-hidden"/>
                    </div>
                    <div>
                        <label>Email</label>
                        <span id="comment-email" class="text">&nbsp;</span>
                        <input type="hidden" name="email" value="" id="comment-email-hidden"/>
                    </div>
                    <div>
                        <label>URL</label>
                        <span id="comment-url" class="text">&nbsp;</span>
                        <input type="hidden" name="website" value="" id="comment-url-hidden"/>
                    </div>
                </div>
            </div>

            <textarea rows="4" name="comment"></textarea>

        </div>

        <input type="submit" />
    </form>
    
    <div id="comment-container"></div>
</div>