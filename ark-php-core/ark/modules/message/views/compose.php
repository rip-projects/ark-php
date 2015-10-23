<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(humanize(get_class($CI)))) ?>
<?php $user_id = $CI->auth->get_user() ?>
<?php echo $this->admin_panel->breadcrumb(array(
    array('uri' => "message/inbox/" . $user_id['id'], 'title' => humanize(get_class($CI))),
    array('uri' => $CI->uri->uri_string, 'title' => "Send Messages"),
)) ?>

<style type="text/css">
  #tos-holder {
    float: left;
    display: table;
  }
  #tos-holder > div {
    display: none;
  }
  #tos-holder .to-show {
    display: block;
    float: left;
    border: 1px solid #e6e6e6;
    background-color: #ffffff;
    line-height: 25px;
    height: 25px;
    color: #808080;
    margin: 0 5px 0 0;
    padding: 0 30px 0 10px;
    position: relative;
  }
  #tos-holder .to-show a {
    position: absolute;
    top: 5px;
    right: 5px;
    background: url("<?php echo theme_url('img/icons/black/clear.png') ?>") no-repeat;
    width: 16px;
    height: 16px;
    text-indent: -9999px;
    direction: ltr;
    overflow: hidden;
  }
</style>

<div class="clearfix"></div>

<fieldset>
    <form action="<?php echo current_url() ?>" method="post" class="ajaxform" >
    <legend>Messages</legend>
        <div>
            <label class="mandatory">Recipients</label>
            <input type="text" id="to-add" placeholder="Search message recipient" />
        </div>
        <div>
            <label>&nbsp;</label>
            <div id="tos-holder">
                <?php if (!empty($_POST['tos'])) : ?>
                    <?php foreach ($_POST['tos'] as $to): ?>
                        <div class="to-show" id="<?php echo $to ?>">
                            <?php echo $to ?>
                            <a href="#<?php echo $to ?>" class="to-del" title="Remove Recipient">x</a>
                            <input type="hidden" name="tos[]" value="<?php echo $to ?>">
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
                            
                        $('div.to-show a').click(delBtn.click).tooltip(delBtn.tooltip);
                            
                        var processTags = function(row) {
                            if (!row) {
                                return;
                            }
                            if ($('.to-show#' + row.id).length > 0) {
                                $('#to-add').val('').focus();
                                return;
                            }
                            
                            var inp = $('<input type="hidden" name="tos[]" />').val(row.id);
                            var btn = $('<a href="#' + row.id + '" class="to-del" title="Remove Recipient">x</a>').click(delBtn.click).tooltip(delBtn.tooltip);
                            var hid = $('<div class="to-show" id="' + row.id + '">' + row.name + '</div>').append(btn).append(inp);
                            

                            $('#tos-holder').append(hid);
                            $('#to-add').val('').focus();
                        }
                        var options = {
                            dataType: 'json',
                            minChars: 0,
                            max: 15,
                            autoFill: true,
                            mustMatch: true,
                            matchContains: false,
                            selectFirst: true,
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
                        $('#to-add').autocomplete("<?php echo site_url('rpc/tos') ?>", options).result(function(evt, row, value) {
                            processTags(row);
                        });
                    });
                </script>
            </div>
        </div>
        <div>
            <label class="mandatory">Subject</label>
            <input type="text" name="subject" value="<?php echo set_value('subject') ?>">
        </div>
        <div>
            <label class="mandatory">Body</label>
            <textarea name="body"><?php echo set_value('body') ?></textarea>
        </div>
    </fieldset>
    
    <div class="action-buttons btn-group" >
        <input type="submit" class="btn" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Cancel') ?></a>
    </div>
    
</form>
