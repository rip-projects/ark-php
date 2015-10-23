<style type="text/css">
    fieldset.desktop { text-align:center }
    fieldset.desktop .item { padding: 10px 0; }
    fieldset.desktop .item .wrap { overflow: hidden; }
    fieldset.desktop .item a { text-decoration: none!important;  display: block; }
    fieldset.desktop .item h3 { font-size: 1em; padding: 0; margin: 0; white-space: nowrap; text-overflow:ellipsis; overflow:hidden}
    fieldset.desktop .item.hover { background-color: #F9F9F9; border-radius: 20px; box-shadow:0 3px 10px rgba(0,0,0,0.25) inset }
</style>

<?php $menu_arr = ($grouping) ? $menus : array( array('title' => 'Menu', 'children' => $menus) ) ?>
<?php foreach($menu_arr as $m): ?>
    <?php $menus = $m['children'] ?>
<fieldset class="desktop">
    <legend><?php echo l($m['title']) ?></legend>
<?php foreach($menus as $k => $menu): ?>
    <?php if ($k % $col == 0): ?>
    <div class="layout-flexible">
    <?php endif ?>
        <div class="<?php echo $classes[$col] ?> item">
            <a href="<?php echo site_url($menu['uri']) ?>" title="<?php echo l($menu['title']) ?>">
                <img src="<?php echo theme_url($menu['image']) ?>" width="100" height="100" alt="<?php echo l($menu['title']) ?>" />
                <div class="clear"></div>
                <h3><?php echo l($menu['title']) ?></h3>
            </a>
        </div>
    <?php if ($k % $col == $col-1): ?>
    </div>
    <?php endif ?>
<?php endforeach ?>

<?php if (count($menus) % $col != 0):  ?>
    <div class="auto"></div>
    </div>
<?php endif ?>
</fieldset>
<?php endforeach ?>

<script type="text/javascript">
    $(function() {
        var w = $('fieldset.desktop .item.<?php echo $classes[$col] ?> .wrap').eq(0).width();
        $('fieldset.desktop .item.<?php echo $classes[$col] ?> h3').css({
            'width': w + 'px'
        });
    });
</script>