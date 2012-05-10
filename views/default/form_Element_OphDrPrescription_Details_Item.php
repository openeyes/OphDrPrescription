<tr>
	<td>
		<?php echo $item->drug->name; ?>
		<?php if($item->id) { ?><input type="hidden" name="prescription_item[<?php echo $key ?>][id]" value="<?php echo $item->id?>" /><?php } ?>
		<input type="hidden" name="prescription_item[<?php echo $key ?>][drug_id]" value="<?php echo $item->drug_id?>" />
	</td>
	<td>
		TODO
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][route_id]', null, array(0 => 'Topical'), array('empty' => 'Select Route')); ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][eye_id]', null, array(0 => 'LE'), array('empty' => 'Select Eye')); ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][frequency_id]', null, array(0 => 'qid'), array('empty' => 'Select Frequency')); ?>
	</td>
	<td>
		<?php echo CHtml::dropDownList('prescription_item['.$key.'][duration_id]', null, array(0 => '1 Month'), array('empty' => 'Select Duration')); ?>
	</td>
	<td>
		<a class="removeItem"	href="#">Remove</a>
	</td>
</tr>
