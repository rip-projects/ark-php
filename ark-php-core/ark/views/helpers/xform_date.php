<?php $id = uniqid('date_') ?>
<?php if ($include_first): ?>
	<script type="text/javascript" src="<?php echo theme_url('js/datepicker.js') ?>"></script>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo theme_url('css/datepicker.css') ?>" />
<?php endif ?>
<div id="<?php echo $id ?>" class="component-placeholder">
    <input type="text" class="date <?php echo (isset($options['class'])) ? $options['class'] : '' ?>" placeholder="<?php echo humanize($name) ?>"
		<?php echo (!empty($post[$name])) ? 'value="'. $post['humandate_'.$name] . '"' : '' ?>
    	<?php echo $extra ?> />
    <?php if (isset($options['show_time']) && $options['show_time']): ?>
    <input type="text" class="time-val <?php echo (isset($options['class'])) ? $options['class'] : '' ?>" value="<?php echo (!empty($post[$name])) ? $post['humantime_'.$name] : '00:00:00' ?>" />
    <?php endif ?>
    <input type="hidden" class="hidden-val" value="<?php echo @$post[$name] ?>" name="<?php echo $name ?>" />
</div>

<script type="text/javascript">

(function() {
    $(function(){
    	var $date = $('#<?php echo $id ?> .date');
        var $time = $('#<?php echo $id ?> .time-val');
    	var $hidden = $('#<?php echo $id ?> .hidden-val');

    	var isValidDate = function(dtStr) {
    		var dtArr = dtStr.split('/');
    		var dt = new Date(
    			parseInt(dtArr[2], 10),
    			parseInt(dtArr[1], 10) - 1,
    			parseInt(dtArr[0], 10)
    		);
    		return (!isNaN(dt.getTime()));
    	}

    	$date.DatePicker({
    		format:'d/m/Y',
    		date: $date.val(),
    		current: $date.val(),
    		starts: 1,
    		position: 'l',
    		onBeforeShow: function() {
    			var dtStr = $date.val();
    			if (!isValidDate(dtStr)) {
    				var now = new Date();
    				dtStr = now.format('dd/mm/yyyy');
    				// $date.val(dtStr);
    			}
    			$date.DatePickerSetDate(dtStr, true);
    		},
    		onChange: function(formatted, dt){
    			if ($date.val(formatted).attr('checked',true)) {
    				$hidden.val(dt.format('yyyy-mm-dd'));
    				$date.DatePickerHide();
    			}
    		}

    	});
        var fn = function(evt) {
            var dtStr = $date.val();
            if (!dtStr || dtStr == '') {
                $hidden.val('');
                // $time.val('00:00:00');
            } else {
                if (!isValidDate(dtStr)) {
                    var now = new Date();
                    dtStr = now.format('dd/mm/yyyy');
                    $date.val(dtStr);
                }

                var t = $time.val().split(':');

                var dtArr = dtStr.split('/');
                var dt = new Date(
                    parseInt(dtArr[2], 10),
                    parseInt(dtArr[1], 10) - 1,
                    parseInt(dtArr[0], 10),
                    parseInt(t[0] || 0, 10),
                    parseInt(t[1] || 0, 10),
                    parseInt(t[2] || 0, 10)
                );

                $time.val(dt.format('HH:MM:ss'));
                $hidden.val(dt.format('yyyy-mm-dd HH:MM:ss'));
            }
        };

    	$date.bind('change', fn);
        $time.bind('change', fn);
    });
})();

</script>