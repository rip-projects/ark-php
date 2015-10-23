<script type="text/javascript">
	Number.prototype.formatMoney = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	 };

	<?php if (empty($vessel)): ?>
	var VESSEL = {};
	<?php else: ?>
	var VESSEL = <?php echo json_encode($vessel) ?>;
	<?php endif ?>
</script>

<?php echo $CI->_breadcrumb(1) ?>
<div class="clearfix"></div>

<form method="post">
	<?php echo $this->load->view($CI->uri->rsegments[1].'/summary', '', 1) ?>

	<?php if ($CI->payment_enabled === -1 || $CI->reply_enabled === -1): ?>
	<fieldset>
		<legend><?php echo l('Configuration') ?></legend>
		<?php if ($CI->payment_enabled === -1): ?>
		<div>
			<label><?php echo l('Use Payment') ?></label>
			<?php echo form_checkbox('use_payment', '1', @$_POST['use_payment']) ?>
		</div>
		<script type="text/javascript">
			$(function() {
				var replyChanged = function() {
					if ($('input[name=use_payment]').attr('checked')) {
						$('#payment-placeholder').show();
					} else {
						$('#payment-placeholder').hide();
					}
				};
				$('input[name=use_payment]').change(replyChanged);
				replyChanged();
			});
		</script>
		<?php endif ?>
		<?php if ($CI->reply_enabled === -1): ?>
		<div>
			<label><?php echo l('Use Reply') ?></label>
			<?php echo form_checkbox('use_reply', '1', @$_POST['use_reply']) ?>
		</div>
		<script type="text/javascript">
			$(function() {
				var replyChanged = function() {
					if ($('input[name=use_reply]').attr('checked')) {
						$('#reply-placeholder').show();
					} else {
						$('#reply-placeholder').hide();
					}
				};
				$('input[name=use_reply]').change(replyChanged);
				replyChanged();
			});
		</script>
		<?php endif ?>
	</fieldset>
	<?php endif ?>

	<?php if ($CI->payment_enabled !== 0): ?>	
		<fieldset id="payment-placeholder">
			<legend><?php echo l('Payment') ?></legend>
			<?php if (!empty($vessel)): ?>
			<div>
				<label><?php echo l('Vessel Name') ?></label>
				<span><?php echo $vessel['name'] ?></span>
			</div>
			<div>
				<label><?php echo l('GRT') ?></label>
				<span><?php echo format_number($vessel['grt']) ?></span>
			</div>
			<?php endif ?>
			<div class="grid-container">
				<table class="grid table table-hover table-striped table-condensed">
					<tr>
						<th><?php echo l('Payment') ?></th>
						<th>&nbsp;</th>
						<th><?php echo l('Price') ?></th>
						<th><?php echo l('Unit') ?></th>
						<th><?php echo l('Amount') ?></th>
						<th><?php echo l('Total') ?></th>
						<th>&nbsp;</th>
					</tr>
				</table>
			</div>
			<a href="#" class="btn" id="btn-payment"><?php echo l('Add Payment') ?></a>
			<script type="text/javascript">
				function addRow(row) {
					var $tr = $(_.template($('#payment-template').html())());
					$('#payment-placeholder .grid tbody').append($tr);

					if (row) {
						var $select_t = $tr.find('select.payment_type');
						var $select_i = $tr.find('select.payment_item');
						var $input = $tr.find('input[name="amount[]"]');

						$select_t.val(row.payment_type).trigger('change');
						setTimeout(function() {
							$select_i.val(row.payment_item).trigger('change');
							$input.val(row.amount);
						}, 500);
					}

					xn.helper.stylize('#payment-placeholder');
				}

				function onChange() {
					var $tr = $(this).parents('tr');
					try {
						var $select = $tr.find('select.payment_item');
						var $input = $tr.find('input[name="amount[]"]');
						var selection = JSON.parse(($select.attr('data-selection')) ? $select.attr('data-selection') : '{}');
						var row = (selection[$select.val()]) ? selection[$select.val()] : null;
						if (row) {
							$tr.find('.total').html(row.currency_name + ' ' + parseFloat(row.price * $input.val()).formatMoney() );
						} else {
							$tr.find('.price').html('');	
							$tr.find('.total').html('');
							$tr.find('.unit').html('');
						}
					} catch(e) {
						$tr.find('.price').html('');	
						$tr.find('.total').html('');
						$tr.find('.unit').html('');
					}
				}

				$(function() {
					var post = <?php echo json_encode($_POST) ?>;
					if (post.payment_type) {
						for(var i = 0; i < post.payment_type.length; i++) {
							addRow({
								payment_type: (post.payment_type[i]) ? post.payment_type[i] : '',
								payment_item: (post.payment_item[i]) ? post.payment_item[i] : '',
								amount: (post.amount[i]) ? post.amount[i] : '',
							});	
						}
					} else {
						addRow();
					}
				});

				$('#btn-payment').live('click', function(evt) {
					addRow();
					return evt.preventDefault();
				});

				$('.btn-delete').live('click', function(evt) {
					var $tr = $(this).parents('tr');
					$tr.remove();
					if ($('#payment-placeholder .grid tr').length <= 1) {
						addRow();
					}
					return evt.preventDefault();
				});

				$('.payment_type').live('change', function() {
					var that = this;
					var $tr = $(this).parents('tr');
					var $select = $tr.find('select.payment_item');
					if ($(this).val()) {
						$.get('<?php echo site_url("rpc/rpc_lookup_price_list") ?>/' + $(this).val(), function(data) {
							$select.html('<option value=""><?php echo l('(Please select)') ?></option>');
							var selection = {};
							for (var i = 0; i < data['items'].length; i++) {
								$select.append('<option value="' + data['items'][i]['id'] + '">' + data['items'][i]['description'] + '</option>');
								selection[data['items'][i]['id']] = data['items'][i];
							}
							$select.attr('data-selection', JSON.stringify(selection));

							onChange.call(that);
						}, 'json');
					} else {
						$select.html('<option value=""><?php echo l('(Please select)') ?></option>');
						$select.attr('data-selection', '');
						$tr.find('.payment_item').trigger('change');
					}
				});

				$('.payment_item').live('change', function() {
					var $tr = $(this).parents('tr');
					var priceStr = '';
					var unitStr = '';

					var selectionData = JSON.parse(($(this).attr('data-selection')) ? $(this).attr('data-selection') : '{}');
					var item = (selectionData[$(this).val()]) ? selectionData[$(this).val()] : null;
					if (item) {
						priceStr = item['currency_name'] + ' ' + parseFloat(item['price']).formatMoney();
						unitStr = item['unit_name'];
						$tr.find('input[name="amount[]"]').val((item.unit == 1) ? VESSEL.grt : 1);
					}
					$tr.find('.price').html(priceStr);
					$tr.find('.unit').html(unitStr);
				});

				$('#payment-placeholder .grid tr select, #payment-placeholder .grid tr input').live('change', onChange);
			</script>
		</fieldset>
	<?php endif ?>

	<?php if ($CI->reply_enabled !== 0): ?>
		<fieldset id="reply-placeholder">
			<legend><?php echo l('Reply') ?></legend>
			
			<div>
				<label><?php echo l('Mail Code') ?></label>
				<?php echo form_dropdown('mail_code_id', $mail_code_items) ?>
			</div>
		</fieldset>
	<?php endif ?>

	<div class="action-buttons btn-group">
		<?php echo $CI->_buttons() ?>
	</div>
</form>

<script type="text/template" id="payment-template">
	<tr>
		<td style="width:1px"><?php echo form_dropdown('payment_type[]', $payment_type_options, @$_POST['payment_type'][$i], 'class="payment_type medium"') ?></td>
		<td style="width:1px"><?php echo form_dropdown('payment_item[]', array('' => l('(Please select)')), @$_POST['payment_item'][$i], 'class="payment_item medium"') ?></td>
		<td style="text-align: right"><span class="price"></span></td>
		<td style="text-align: right"><span class="unit"></span></td>
		<td style="width:1px"><input type="text" name="amount[]" class="medium" value="1" style="text-align: right" /></td>
		<td style="text-align: right"><span class="total"></span></td>
		<td class="submenu" style="width:1px">
			<span class="delete">
				<a href="" class="btn-delete">Delete</a>
			</span>
		</td>
	</tr>
</script>
