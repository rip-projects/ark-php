<?php $uniqid = uniqid('autocomplete_'.$ID.'_') ?>
<?php if (!$ID): ?>

<!-- init form autocomplete -->
<script type='text/javascript' src="<?php echo theme_url('js/jquery.autocomplete.js') ?>"></script>
<script type="text/javascript">
    $.Autocompleter.defaults = {
        dataType: 'json',
        inputClass: "ac_input",
        resultsClass: "ac_results",
        loadingClass: "ac_loading",
        minChars: 1,
        delay: 400,
        matchCase: false,
        matchSubset: true,
        matchContains: false,
        cacheLength: 10,
        max: 100,
        mustMatch: true,
        extraParams: {},
        selectFirst: true,
        parse: function(data) {
            var d = [];
            for(var i = 0; data && i < data.length; i++) {
                var k = Object.keys(data[i]) || ['', ''];
                var key = (data[i].key) ? '' + data[i].key : data[i][k[0]] || '';
                var value = (data[i].value) ? '' + data[i].value : data[i][k[1]] || '';

                var r = {
                    data: data[i],
                    value: key,
                    result: value
                };
                d.push(r);
            }
            return d;
        },
        formatItem: function(row) {
            var k = Object.keys(row);
            var kKey = (typeof(row.value) == 'undefined') ? (k[1] || k[0]) : 'value';
            return row[kKey];
        },
        formatMatch: null,
        autoFill: true,
        width: 0,
        multiple: false,
        multipleSeparator: ", ",
        highlight: function(value, term) {
            return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>");
        },
        scroll: true,
        scrollHeight: 220
    };
</script>
<!-- // init form autocomplete -->

<?php endif ?>

<div id="<?php echo $uniqid ?>" style="display: inline">
    <?php echo form_input('_'.$data['name'], $selected, $extra) ?>
    <input type="hidden" name="<?php echo $data['name'] ?>" value="<?php echo set_value($data['name']) ?>" />
</div>
<script type="text/javascript">
    $(function() {
        <?php if (is_array($data['options'])): ?>

        var data = <?php echo json_encode($data['options']) ?>;
        var options = {
            minChars: 0,
            max: 15,
            autoFill: true,
            mustMatch: false,
            matchContains: false,
            selectFirst: true,
            scrollHeight: 220
        };

        <?php else: ?>

        var data = "<?php echo $data['options'] ?>";
        var options = {
            dataType: 'json',
            minChars: 0,
            max: 15,
            autoFill: true,
            mustMatch: false,
            matchContains: false,
            selectFirst: true,
            scrollHeight: 220
        };

        <?php endif ?>
        $('#<?php echo $uniqid ?> input[type=text]').autocomplete(data, options).result(function(evt, row, key) {
            $('#<?php echo $uniqid ?> input[type=hidden]').val(key);
        });
    });
</script>