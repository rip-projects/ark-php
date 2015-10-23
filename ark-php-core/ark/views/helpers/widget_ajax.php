<script type="text/javascript">
    $(function() {
        var p = $("#<?php echo $id ?>").parent();
        p.addClass('loading');
        $("#<?php echo $id ?>").load("<?php echo site_url($url) ?>", function() {
            <?php echo ($callback) ? $callback : '' ?>;
            p.removeClass('loading');
        });
    });
</script>
<div id="<?php echo $id ?>"<?php echo $attr_str ?> class="widget-ajax"></div>