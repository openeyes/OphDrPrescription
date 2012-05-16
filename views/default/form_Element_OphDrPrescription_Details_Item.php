<tr data-key="<?php echo $key ?>" class="prescriptionItem<?php if($patient->hasAllergy($item->drug_id)) { ?> allergyWarning<?php } ?>">
	<td>
		<?php echo $item->drug->label; ?>
		<?php if($item->id) { ?><input type="hidden" name="prescription_item[<?php echo $key ?>][id]" value="<?php echo $item->id?>" /><?php } ?>
		<input type="hidden" name="prescription_item[<?php echo $key ?>][drug_id]" value="<?php echo $item->drug_id?>" />
	</td>
	<td class="prescriptionItemDose">
		<?php echo CHtml::textField('prescription_item['.$key.'][dose]', $item->dose) ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][route_id]', $item->route_id, CHtml::listData($item->availableRoutes(), 'id', 'name'), array('empty' => '-- Select --', 'class' => 'drugRoute')); ?>
	</td>
	<td>
		<?php if($item->route && $options = $item->route->options) {
			echo CHtml::dropDownList('prescription_item['.$key.'][route_option_id]', $item->route_option_id, CHtml::listData($options, 'id', 'name'), array('empty' => '-- Select --'));
		} else {
			echo '-';
		}?>
	</td>
	<td class="prescriptionItemFrequencyId">
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][frequency_id]', $item->frequency_id, CHtml::listData($item->availableFrequencies(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td class="prescriptionItemDurationId">
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][duration_id]', $item->duration_id, CHtml::listData($item->availableDurations(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td>
		<a class="removeItem"	href="#">Remove</a>
		| <a class="taperItem"	href="#">+Taper</a>
	</td>
</tr>
<?php
	$count = 0;
	foreach($item->tapers as $taper) {
?>
<tr data-key="<?php echo $key ?>" data-taper="<?php echo $count ?>" class="prescriptionTaper">
	<td class="prescriptionLabel">
		then
		<?php if($taper->id) { ?><input type="hidden" name="prescription_item[<?php echo $key ?>][taper][<?php echo $count ?>][id]" value="<?php echo $taper->id?>" /><?php } ?>
	</td>
	<td>
		<?php echo CHtml::textField('prescription_item['.$key.'][taper]['.$count.'][dose]', $taper->dose) ?>
	</td>
	<td colspan="2"></td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][taper]['.$count.'][frequency_id]', $taper->frequency_id, CHtml::listData($item->availableFrequencies(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][taper]['.$count.'][duration_id]', $taper->duration_id, CHtml::listData($item->availableDurations(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td>
		<a class="removeTaper"	href="#">Remove</a>
	</td>
</tr>
<?php
		$count++;
	}
?>
