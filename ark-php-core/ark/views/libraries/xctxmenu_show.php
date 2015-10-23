<?php $ctxid = uniqid('xctxmenu_') ?>

<script src="<?php echo theme_url('js/jquery.contextMenu.js') ?>" type="text/javascript"></script>
<link href="<?php echo theme_url('css/jquery.contextMenu.css') ?>" rel="stylesheet" type="text/css" />

<ul id="<?php echo $ctxid ?>" class="contextMenu">
    <?php foreach($self->actions as $key => $action): ?>
        <li class="<?php echo $key ?>"><a href="#<?php echo $key ?>"><?php echo humanize($key) ?></a></li>
    <?php endforeach ?>
</ul>

<script type="text/javascript">
    $(function() {
        var actions = <?php echo json_encode($self->actions) ?>;
        $('<?php echo $selector ?>').contextMenu({
            menu: '<?php echo $ctxid ?>'
        }, function(action, el, pos) {

            <?php if (!empty($self->callback)): ?>
                <?php echo $self->callback ?>(action, el, pos);
            <?php else: ?>
                var selectedList = [];
                selectedList.push($(el).attr('data-ref'));

                $(el).parents('tbody').find('*[checked]').parents('tr').each(function(index, node) {
                    if (selectedList[0] != $(node).attr('data-ref')) {
                        selectedList.push($(node).attr('data-ref'));
                    }
                });
                if(actions[action].search(/^callback/i) >= 0) {
                	$acts = actions[action].split(':');
                	eval($acts[1]+'(action, el, pos)');
                } else {   
                window.location.href = xn.helper.createUrl(actions[action] + '/' + selectedList.join(','));
                }
            <?php endif ?>
        });
    });
</script>