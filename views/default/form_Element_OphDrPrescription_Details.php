<div class="<?php echo $element->elementType->class_name?>">
	<div id="div_Element_OphDrPrescription_Details_prescription_items">
		<div class="eventDetail">
			<div class="label">Add Item</div>
			<div class="data split limitWidth">
				<div class="left">
					<?php echo CHtml::dropDownList('common_drug_id', null, CHtml::listData($element->commonDrugs(), 'id', 'label'), array('empty' => '-- Select common --')); ?>
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
							'htmlOptions' => array(
								'placeholder' => 'or search formulary',
							),
					));
					?>
				</div>
				<div class="right">
					<h5 class="normal"><em>Filtered by:</em></h5>
					<span class="group">Type
					<?php echo CHtml::dropDownList('drug_type_id', null, CHtml::listData($element->drugTypes(), 'id', 'name'), array('class' => 'drugFilter', 'empty' => '-- Select --')); ?>
					</span>
					<span class="group">No Preservative
					<?php echo CHtml::checkBox('preservative_free', null, array('class' => 'drugFilter'))?>
					</span>
				</div>
			</div>
		</div>
		<div class="eventDetail">
			<div class="label">Add Standard Set</div>
			<div>
				<?php echo CHtml::dropDownList('drug_set_id', null, CHtml::listData($element->drugSets(), 'id', 'name'), array('empty' => '-- Select --')); ?>
			</div>
		</div>
		<div class="eventDetail">
			<div class="label">Other Actions</div>
			<div>
				<?php if($this->getPreviousPrescription($this->patient, $element->id)) { ?>
				<button type="button" class="classy blue mini"
					id="repeat_prescription" name="repeat_prescription">
					<span class="button-span button-span-blue">Add Repeat Prescription</span>
				</button>
				<?php } ?>
				<button type="button" class="classy blue mini"
					id="clear_prescription" name="clear_prescription">
					<span class="button-span button-span-blue">Clear Prescription</span>
				</button>
			</div>
		</div>
		<div class="grid-view">
			<input type="hidden" name="prescription_items_valid" value="1" />
			<table id="prescription_items">
				<thead>
					<tr>
						<th>Drug</th>
						<th>Dose</th>
						<th>Route</th>
						<th>Options</th>
						<th>Frequency</th>
						<th>Duration</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($this->getPrescriptionItems($element) as $key => $item) {
						$this->renderPartial('form_Element_OphDrPrescription_Details_Item', array('key' => $key, 'item' => $item, 'patient' => $this->patient));
						} ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php echo $form->textArea($element, 'comments', array('rows' => 4, 'cols' => 60)) ?>
</div>

<script type="text/javascript">

	// Initialise variables
	var item_count = $('#prescription_items tbody tr').length;
	var patient_id = <?php echo $this->patient->id ?>

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
	$('#prescription_items input[name$="[drug_id]"]').each(function(index) {
		var option = $('#common_drug_id option[value="' + $(this).val() + '"]');
		if(option) {
			option.data('used', true);
		}
	});
	applyFilter();
	
	// Add selected common drug to prescription
	$('body').delegate('#common_drug_id', 'change', function() {
		var selected = $(this).children('option:selected');
		if(selected.val().length) {
			addItem(selected.text(), selected.val());
			$(this).val('');
		}
		return false;
	});

	// Add selected drug set to prescription
	$('body').delegate('#drug_set_id', 'change', function() {
		var selected = $(this).children('option:selected');
		if(selected.val().length) {
			addSet(selected.val());
			$(this).val('');
		}
		return false;
	});

	// Add repeat to prescription
	$('body').delegate('#repeat_prescription', 'click', function() {
		addRepeat();
		return false;
	});

	// Clear prescription
	$('body').delegate('#clear_prescription', 'click', function() {
		$('#prescription_items tbody tr').remove();
		$('#common_drug_id option').data('used', false);
		item_count = 0;
		applyFilter();
		return false;
	});

	// Update drug route options for selected route
	$('body').delegate('select.drugRoute', 'change', function() {
		var selected = $(this).children('option:selected');
		if(selected.val().length) {
			var options_td = $(this).parent().next();
			var key = $(this).closest('tr').attr('data-key');
			$.get("/OphDrPrescription/Default/RouteOptions", { key: key, route_id: selected.val() }, function(data) {
				options_td.html(data);
			});
		}
		return false;
	});

	// Remove item from prescription
	$('#prescription_items').delegate('a.removeItem', 'click', function() {
		var row =  $(this).closest('tr');
		var drug_id = row.find('input[name$="[drug_id]"]').first().val();
		var key = row.attr('data-key');
		$('#prescription_items tr[data-key="'+key+'"]').remove();
		var option = $('#common_drug_id option[value="' + drug_id + '"]');
		if (option) {
			option.data('used', false);
			applyFilter();
		}
		return false;
	});

	// Add taper to item
	$('#prescription_items').delegate('a.taperItem:not(.processing)', 'click', function() {
		var row = $(this).closest('tr');
		var key = row.attr('data-key');
		var last_row = $('#prescription_items tr[data-key="'+key+'"]').last();
		var taper_key = (last_row.attr('data-taper')) ? parseInt(last_row.attr('data-taper')) + 1 : 0;
		
		// Clone item fields to create taper row
		var dose_input = row.find('td.prescriptionItemDose input').first().clone();
		dose_input.attr('name', dose_input.attr('name').replace(/\[dose\]/, "[taper]["+taper_key+"][dose]"));
		dose_input.attr('id', dose_input.attr('id').replace(/_dose$/, "_taper_"+taper_key+"_dose"));
		var frequency_input = row.find('td.prescriptionItemFrequencyId select').first().clone();
		frequency_input.attr('name', frequency_input.attr('name').replace(/\[frequency_id\]/, "[taper]["+taper_key+"][frequency_id]"));
		frequency_input.attr('id', frequency_input.attr('id').replace(/_frequency_id$/, "_taper_"+taper_key+"_frequency_id"));
		frequency_input.val(row.find('td.prescriptionItemFrequencyId select').val());
		var duration_input = row.find('td.prescriptionItemDurationId select').first().clone();
		duration_input.attr('name', duration_input.attr('name').replace(/\[duration_id\]/, "[taper]["+taper_key+"][duration_id]"));
		duration_input.attr('id', duration_input.attr('id').replace(/_duration_id$/, "_taper_"+taper_key+"_duration_id"));
		duration_input.val(row.find('td.prescriptionItemDurationId select').val());
		
		// Insert taper row
		var new_row = $('<tr data-key="'+key+'" data-taper="'+taper_key+'" class="prescriptionTaper"></tr>');
		new_row.append($('<td class="prescriptionLabel">then</td>'), $('<td></td>').append(dose_input), $('<td colspan="2"></td>'), $('<td></td>').append(frequency_input), $('<td></td>').append(duration_input), $('<td><a class="removeTaper"	href="#">Remove</a></td>'));
		last_row.after(new_row);
		
		return false;
	});

	// Remove taper from item
	$('#prescription_items').delegate('a.removeTaper', 'click', function() {
		var row =  $(this).closest('tr');
		row.remove();
		return false;
	});

	// Apply selected drug filter
	$('body').delegate('.drugFilter', 'change', function() {
		applyFilter();
		return false;
	});

	// Add repeat to prescription
	function addRepeat() {
		var current_item_count = $('#prescription_items tbody tr').length;
		$.get("/OphDrPrescription/Default/RepeatForm", { key: item_count, patient_id: patient_id }, function(data) {
			$('#prescription_items').append(data);
			markUsed();
			applyFilter();
			item_count += $('#prescription_items tbody tr').length - current_item_count;
		});
	}
	
	// Add set to prescription
	function addSet(set_id) {
		var current_item_count = $('#prescription_items tbody tr').length;
		$.get("/OphDrPrescription/Default/SetForm", { key: item_count, patient_id: patient_id, set_id: set_id }, function(data) {
			$('#prescription_items').append(data);
			markUsed();
			applyFilter();
			item_count += $('#prescription_items tbody tr').length - current_item_count;
		});
	}
	
	// Add item to prescription
	function addItem(label, item_id) {
		$.get("/OphDrPrescription/Default/ItemForm", { key: item_count, patient_id: patient_id, drug_id: item_id }, function(data){
			$('#prescription_items').append(data);
		});
		var option = $('#common_drug_id option[value="' + item_id + '"]');
		if (option) {
			option.data('used', true);
			applyFilter();
		}
		item_count++;
	}

	// Mark used common drugs
	function markUsed() {
		$('#prescription_items input[name$="\[drug_id\]"]').each(function(index) {
			var option = $('#common_drug_id option[value="' + $(this).val() + '"]');
			if (option) {
				option.data('used', true);
			}
		});
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
