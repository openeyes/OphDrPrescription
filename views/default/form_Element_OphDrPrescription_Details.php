<div class="<?php echo $element->elementType->class_name?>">
	<h4 class="elementTypeName">
		<?php echo $element->elementType->name ?>
	</h4>
	<div id="div_Element_OphDrPrescription_Details_prescription_items"
		class="eventDetail">
		<div class="label">Items</div>
		<?php echo CHtml::dropDownList('common_drug_id', null, $element->getCommonDrugList(), array('empty' => 'Add a drug')); ?>
		<?php
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => 'drug_id',
			'id' => 'autocomplete_drug_id',
			'source' => $this->createUrl('DrugList'),
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
		<input type="hidden" name="prescription_items_valid" value="1" />
		<table id="prescription_items">
			<thead>
				<tr>
					<th>Drug</th>
					<th>Number</th>
					<th>Route</th>
					<th>Eye</th>
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
	<?php echo $form->textArea($element, 'comments', array('rows' => 4, 'cols' => 60)) ?>
</div>

<script type="text/javascript">

	// Initialise item count
	var item_count = $('#prescription_items tr').length;

	// Disable currently prescribed drugs in dropdown
	$('#prescription_items input[name$="[drug_id]"]').each(function() {
		var option = $('#common_drug_id option[value="' + $(this).val() + '"]');
		if(option) {
			option.attr("disabled", "disabled");
		}
	});
	
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
			option.removeAttr("disabled");
		}
		return false;
	});

	// Add item to prescription
	function addItem(label, item_id) {
		$.get("/OphDrPrescription/Default/ItemForm", { key: item_count, drug_id: item_id }, function(data){
			$('#prescription_items').append(data);
		});
		var option = $('#common_drug_id option[value="' + item_id + '"]');
		if (option) {
			option.attr("disabled", "disabled");
		}
		$(this).val('');
		item_count++;
	}
		
</script>
