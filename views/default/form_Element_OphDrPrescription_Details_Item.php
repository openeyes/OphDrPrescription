<tr>
	<td>
		<?php echo $item->drug->name; ?>
		<?php if($item->id) { ?><input type="hidden" name="prescription_item[<?php echo $key ?>][id]" value="<?php echo $item->id?>" /><?php } ?>
		<input type="hidden" name="prescription_item[<?php echo $key ?>][drug_id]" value="<?php echo $item->drug_id?>" />
	</td>
	<td>
		<?php echo CHtml::textField('prescription_item['.$key.'][dose]', $item->dose) ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][route_id]', $item->route_id, CHtml::listData($item->availableRoutes(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][frequency_id]', $item->frequency_id, CHtml::listData($item->availableFrequencies(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][duration_id]', $item->duration_id, CHtml::listData($item->availableDurations(), 'id', 'name'), array('empty' => '-- Select --')); ?>
	</td>
	<td>
		<a class="removeItem"	href="#">Remove</a>
	</td>
</tr>
