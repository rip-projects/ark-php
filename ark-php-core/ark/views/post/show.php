<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(l(humanize(get_class($CI))))) ?>
<script type="text/javascript" src="<?php echo theme_url('js/tiny_mce/jquery.tinymce.js') ?>"></script>
<script type="text/javascript">
    ajaxfilemanager = function (field_name, url, type, win) {
    var ajaxfilemanagerurl = xn.helper.themeUrl("jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php");
    var view = 'detail';
    switch (type) {
        case "image":
            view = 'thumbnail';
            break;
        case "media":
            break;
        case "flash":
            break;
        case "file":
            break;
        default:
            return false;
    }
    console.log(ajaxfilemanagerurl + "?view=" + view);
    tinyMCE.activeEditor.windowManager.open({
        url: ajaxfilemanagerurl + "?view=" + view,
        width: 782,
        height: 440,
        inline : "yes",
        close_previous : "no"
    },{
        window : win,
        input : field_name
    });
}

    $(function() {
        try {            
            $('textarea.wysiwyg').tinymce({
                // Location of TinyMCE script
                script_url : xn.helper.themeUrl('js/tiny_mce/tiny_mce.js'),

                // General options
                theme : "advanced",
                plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

                // Theme options
                theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,

                // Example content CSS (should be your site CSS)
                content_css : xn.helper.themeUrl("css/global.wysiwyg.css"),

                // Drop lists for link/image/media/template dialogs
                //          template_external_list_url : "lists/template_list.js",
                //          external_link_list_url : "lists/link_list.js",
                //          external_image_list_url : "lists/image_list.js",
                //          media_external_list_url : "lists/media_list.js",

                // Replace values for the template plugin
                template_replace_values : {
                    username : "Some User",
                    staffid : "991234"
                },
                        
                relative_urls : false,
                convert_urls : false,
                        
                file_browser_callback : "ajaxfilemanager"
            });
                
            

        } catch (e) {}

        var titleChanged = function() {
            $.post('<?php echo site_url('rpc/get_post_name') ?>', 'title=' + encodeURI($(this).val()), function(data) {
                $('#name-box').val(data);
            }, 'json');
        }
    
        $('#title-box').change(titleChanged);
        
        
        $('a[href="#toggle"]').toggle(function(evt) {
            tinyMCE.execCommand('mceRemoveControl', false, 'body');
            return evt.preventDefault();
        }, function (evt) {
            tinyMCE.execCommand('mceAddControl', false, 'body');
            return evt.preventDefault();
        });
    });
</script>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>

<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post">
	<fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label class="mandatory">Title</label>
            <input type="text" value="<?php echo set_value('title') ?>" name="title" id="title-box" />
        </div>
        <div>
            <label class="mandatory">Post Name</label>
            <input type="text" value="<?php echo set_value('post_name') ?>" name="post_name" id="name-box" />
        </div>
        <div>
            <label class="mandatory">Body</label>
            <textarea name="body" id="body" class="wysiwyg" style="width: 700px; height: 300px;"><?php echo set_value('body') ?></textarea>
            <div class="clearfix" style="padding-bottom: 10px;"></div>
            <a href="#toggle" class="btn">Toggle Editor</a>
            <div class="clearfix" style="padding-bottom: 10px;"></div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Tags</legend>
        <div>
            <label>Add Tag</label>
            <input type="text" id="tag-add" name="tags[]" placeholder="Input tag and press Enter" />
        </div>
        <div>
            <label>&nbsp;</label>
            <div id="tags-holder">
                <?php if (!empty($_POST['tags'])) : ?>
                    <?php foreach ($_POST['tags'] as $tag): ?>
                        <div class="tag-show" id="<?php echo $tag ?>">
                            <?php echo $tag ?>
                            <a href="#<?php echo $tag ?>" class="tag-del" title="Remove Tag">x</a>
                            <input type="hidden" name="tags[]" value="<?php echo $tag ?>">
                        </div>
                    <?php endforeach ?>
                <?php endif ?>
                <script type="text/javascript">
                    $(function() {
                        var delBtn = {
                            click: function(evt) {
                                $(this).parent().remove();
                                $('.tooltip').remove();
                                console.log(evt);
                                return evt.preventDefault();
                            },
                            tooltip: {
                                effect:'fade'
                            }
                        }
                            
                        $('div.tag-show a').click(delBtn.click).tooltip(delBtn.tooltip);
                            
                        var processTags = function() {
                            var val = $('#tag-add').val();
                                
                            var splitted = val.split(/[ ,;]/);
                            var vals = [];
                            var i, j;
                            for(i = 0; i< splitted.length; i++) {
                                for(j = 0; j < vals.length; j++) {
                                    var skip = false;
                                    if (splitted[i] == '' || splitted[i] == vals[j]) {
                                        skip = true;
                                        break;
                                    }
                                }
                                if (!skip) {
                                    vals[vals.length] = splitted[i];
                                }
                            }
                                
                            $.each(vals, function(i, val) {
                                if ($('.tag-show#' + val).length > 0) {
                                    $('#tag-add').val('').focus();
                                    return evt.preventDefault();
                                }
                                
                                var inp = $('<input type="hidden" name="tags[]" />').val(val);
                                var btn = $('<a href="#' + val + '" class="tag-del" title="Remove Tag">x</a>').click(delBtn.click).tooltip(delBtn.tooltip);
                                var hid = $('<div class="tag-show" id="' + val + '">'+val+'</div>').append(btn).append(inp);
                                
                                // $('#tag-add').parent().append(hid);
                                $('#tags-holder').append(hid);
                            });
                                
                            $('#tag-add').val('').focus();
                        }
                        var options = {
                            dataType: 'json',
                            minChars: 0,
                            max: 15,
                            autoFill: false,
                            mustMatch: false,
                            matchContains: false,
                            selectFirst: false,
                            scrollHeight: 220,
                            parse: function(data) {
                                var array = new Array();
                                for(var i=0;i<data.length;i++) {
                                    array[array.length] = { data: data[i], value: data[i]['name'], result: data[i]['name'] };
                                }
                                return array;
                            },
                            formatItem: function(row) { 
                                console.log(row);
                                return row['name'];
                            }
                        };
                        $('#tag-add').autocomplete("<?php echo site_url('rpc/tags') ?>", options);
                        $('#tag-add').keypress(function(evt) {
                            if (evt.which == 13) {
                                processTags();
                                return evt.preventDefault();
                            }   
                        });
                        
                        $('form').submit(function(evt) {
                            processTags();
                        })
                    });
                </script>
            <div>
        <div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>