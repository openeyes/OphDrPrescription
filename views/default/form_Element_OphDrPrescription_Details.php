<div class="<?php echo $element->elementType->class_name?>">
	<h4 class="elementTypeName">
		<?php echo $element->elementType->name ?>
	</h4>
	<div id="div_Element_OphDrPrescription_Details_prescription_items"
		class="eventDetail">
		<div class="label">Items</div>
		<div class="data">
			<h5>Add Item</h5>
			<div>
				<?php echo CHtml::dropDownList('common_drug_id', null, CHtml::listData($element->commonDrugs(), 'id', 'name'), array('empty' => '-- Select --')); ?>
				or
				<?php
				$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
						'name' => 'drug_id',
						'id' => 'autocomplete_drug_id',
						'source' => "js:function(request, response) {
							$.getJSON('".$this->createUrl('DrugList')."', {
								term : request.term,
								type_id: $('#drug_type_id').val(),
								preservative_free: ($('#preservative_free').is(':checked') ? '1' : ''),
							}, response);
						}",
						'options' => array(
								'select' => "js:function(event, ui) {
									addItem(ui.item.value, ui.item.id);
									$(this).val('');
									return false;
								}",
						),
						'htmlOptions' => array(),
				));
				?>
				(
				Filtered by:
				Type <?php echo CHtml::dropDownList('drug_type_id', null, CHtml::listData($element->drugTypes(), 'id', 'name'), array('class' => 'drugFilter', 'empty' => '-- Select --')); ?>
				,
				No Preservative <?php echo CHtml::checkBox('preservative_free', null, array('class' => 'drugFilter'))?>
				)
			</div>
			<h5>Add Standard Set</h5>
			<div>
					<?php echo CHtml::dropDownList('standard_set_id', null, array(1 => 'Post Op'), array('empty' => '-- Select --')); ?>
			</div>
			<h5>Current Items</h5>
			<div class="grid-view">
				<input type="hidden" name="prescription_items_valid" value="1" />
				<table id="prescription_items">
					<thead>
						<tr>
							<th>Drug</th>
							<th>Dose</th>
							<th>Route</th>
							<th>Frequency</th>
							<th>Duration</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($element->items as $key => $item) {
							$this->renderPartial('form_Element_OphDrPrescription_Details_Item', array('key' => $key, 'item' => $item));
				} ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php echo $form->textArea($element, 'comments', array('rows' => 4, 'cols' => 60)) ?>
</div>

<script type="text/javascript">

	// Initialise item count
	var item_count = $('#prescription_items tr').length;

	// Initialise common drug metadata
	var common_drug_metadata = {
		<?php foreach($element->commonDrugs() as $drug) { ?>
		<?php echo $drug->id?>: {
			"type_id": <?php echo $drug->type_id ?>,
			"preservative_free": <?php echo $drug->preservative_free ?>,
		},
		<?php } ?>
	}

	// Disable currently prescribed drugs in dropdown
	$('#prescription_items input[name$="[drug_id]"]').each(function() {
		var option = $('#common_drug_id option[value="' + $(this).val() + '"]');
		if(option) {
			option.data('used', false);
		}
	});
	applyFilter();
	
	$('body').delegate('#common_drug_id', 'change', function() {
		var selected = $(this).children('option:selected');
		if(selected.val().length) {
			addItem(selected.text(), selected.val());
			$(this).val('');
		}
		return false;
	});

	// Remove item from prescription
	$('#prescription_items').delegate('a.removeItem', 'click', function() {
		var item_id = $(this).closest('tr').find('input[name$="[drug_id]"]').first().val();
		$(this).closest('tr').remove();
		var option = $('#common_drug_id option[value="' + item_id + '"]');
		if (option) {
			option.data('used', false);
			applyFilter();
		}
		return false;
	});

	$('body').delegate('.drugFilter', 'change', function() {
		applyFilter();
		return false;
	});
	
	// Add item to prescription
	function addItem(label, item_id) {
		$.get("/OphDrPrescription/Default/ItemForm", { key: item_count, drug_id: item_id }, function(data){
			$('#prescription_items').append(data);
		});
		var option = $('#common_drug_id option[value="' + item_id + '"]');
		if (option) {
			option.data('used', true);
			applyFilter();
		}
		$(this).val('');
		item_count++;
	}

	// Filter drug choices
	function applyFilter() {
		var filter_type_id = $('#drug_type_id').val();
		var filter_preservative_free = $('#preservative_free').is(':checked');
		$('#common_drug_id option').each(function() {
			var show = true;
			var drug_id = $(this).val();
			if(drug_id) {
				if(filter_type_id && common_drug_metadata[drug_id].type_id != filter_type_id) {
					show = false;
				}
				if(filter_preservative_free && !common_drug_metadata[drug_id].preservative_free) {
					show = false;
				}
				if($(this).data('used')) {
					show = false;
				}
				if(show) {
					$(this).removeAttr("disabled");
				} else {
					$(this).attr("disabled", "disabled");
				}
			}
		});
	}

</script>
