<?php $gridid = uniqid('xgrid_') ?>

<script type="text/javascript">
    // $.fn.xgrid = function() {
    //     var condition = {
    //         position: 0,
    //         component: null,
    //         neighbour: null,
    //         mx: 0
    //     }
    //     // $(this).disableSelection();
    // };
    // $(function() {
    //     $('#<?php echo $gridid ?>').xgrid();
    // });
</script>

<div class="grid-container table-bordered">
    <table class="grid table table-hover table-striped table-condensed" id="<?php echo $gridid ?>">
        <thead>
            <tr class="grid-head-row">
                <?php if ($self->show_checkbox): ?>
                    <th class="xx-short" style="width: 1px"><input type="checkbox" class="grid_head" /></th>
                <?php endif ?>

                <?php foreach ($self->fields as $key => $field): ?>
                    <th <?php echo (empty($self->aligns[$key])) ? '' : 'style="text-align:' . $self->aligns[$key] . '"' ?> class="auto <?php echo (isset($self->classes[$key])) ? $self->classes[$key] : '' ?>">
                        <?php if (!empty($self->sorts[$key])): ?>
                            <a href="<?php echo $self->_sort_uri($field) ?>" class="<?php echo (!empty($self->sort[$field])) ? $self->sort[$field] : '' ?>">
                        <?php endif ?>
                            <?php echo l(!empty($self->names[$key]) ? $self->names[$key] : humanize($field)) ?>
                        <?php if (!empty($self->sorts[$key])): ?>
                            </a>
                        <?php endif ?>
                    </th>
                <?php endforeach ?>
                <?php if (!empty($self->actions)): ?>
                    <th class="grid-action-cell"><span>&nbsp;</span></th>
                <?php endif ?>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($data)): ?>
                <tr class="grid-empty-row">
                    <td colspan="<?php echo count($self->fields) + ( (empty($self->actions)) ? 1 : 2) ?>" style="text-align: center"><span><?php echo l('No record available') ?></span></td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <tr data-ref="<?php echo (isset($row['id'])) ? $row['id'] : '' ?>" class="grid_row">
                        <?php if ($self->show_checkbox): ?>
                        <td><input type="checkbox" name="row[]" value="<?php echo $row['id'] ?>" class="grid_body" /></td>
                        <?php endif ?>
                        <?php for ($i = 0; $i < count($self->fields); $i++): ?>
                            <td <?php echo (empty($self->aligns[$i])) ? '' : 'style="text-align:' . $self->aligns[$i] . '"' ?> class="<?php echo (empty($self->classes[$i])) ? '' : $self->classes[$i] ?>">
                                <span><?php echo $self->highlight($row, $i, ((!empty($filter['q'])) ? $filter['q'] : '')) ?></span>
                            </td>
                        <?php endfor ?>
                        <?php if (!empty($self->actions)): ?>
                            <td class="submenu">
                                <div class="submenu-container">
                                    <?php foreach ($self->actions as $key => $action): ?>
                                        <?php if ($CI->_model('user')->privilege($action)): ?>
                                        <span class="<?php echo $key ?>"><a class="grid-action" href="<?php echo site_url($action . '/' . $row['id']) ?>" title="<?php echo l(humanize($key)) ?>"><?php echo l(humanize($key)) ?></a></span>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </div>
                            </td>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>

    <script type="text/javascript">
        (function() {
            $(function() {
                $("#<?php echo $gridid ?> .grid-action-cell").width($('.submenu-container').width() + 10);

                $("#<?php echo $gridid ?> .grid_head").click(function() {
                    var checkers = $("#<?php echo $gridid ?> .grid_body").attr("checked", $(this).attr("checked") || false).trigger('change');
                });

                $("#<?php echo $gridid ?> .grid_body").change(function() {
                    if ($(this).attr("checked")) {
                        $(this).parents('tr').addClass('selected');
                    } else {
                        $(this).parents('tr').removeClass('selected');
                    }
                });

                $("#<?php echo $gridid ?> tr[data-ref] td:not(:first-child):not(:last-child)").click(function() {
                    var checkers;
                    checkers = $(this).parent().find('.grid_body').attr('checked', true).trigger('change');
                });

                // $("#<?php echo $gridid ?> tr[data-ref] td:not(:first-child):not(:last-child)").mousedown(function(evt) {
                //     if (evt.which == 3) {
                //         var x = $(this).parent().find('.grid_body');
                //         if (!x.attr('checked')) {
                //             var checkers;
                //             checkers = $(this).parent().find('.grid_body').attr('checked', true).trigger('change');
                //         }
                //     }
                // });
            });
        })();
    </script>

    <?php if (!empty($self->custom_script)): ?>
    <script type="text/javascript">
        (function() {
            <?php echo $self->custom_script ?>
        }).apply($('#<?php echo $gridid ?>'));
    </script>
    <?php endif ?>
</div>