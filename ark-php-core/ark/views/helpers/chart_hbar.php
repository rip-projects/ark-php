<?php if (!$loaded): ?>
<script type="text/javascript" src="<?php echo theme_url('vendor/xchart/xchart.js') ?>"></script>
<?php endif ?>

<style type="text/css">
    #<?php echo $uniqid ?> {
        <?php if (!empty($options['height'])): echo 'height: '.$options['height']; endif ?> 
    }
    #<?php echo $uniqid ?> svg {}
</style>

<div id="<?php echo $uniqid ?>">

</div>

<script type="text/javascript">
    $(function() {
        var data = <?php echo json_encode($data) ?>;
        var options = <?php echo json_encode($options) ?>;
        var chart = $('#<?php echo $uniqid ?>').chart(data, options);
    });
</script>
