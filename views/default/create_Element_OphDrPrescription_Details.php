<div class="<?php echo $element->elementType->class_name?>">
	<h4 class="elementTypeName">
		<?php echo $element->elementType->name ?>
	</h4>
	<?php echo CHtml::dropDownList('drug_id', null, $element->getCommonDrugList(), array('empty' => 'Select a drug')); ?>
	<div id="prescription_items_selected">
	<?php foreach($element->items as $item) { ?>
	Drug: <?php echo $item->drug_id ?><br/>
	<?php } ?>
	</div>
	<?php echo $form->textArea($element, 'comments', array('rows' => 4, 'cols' => 60)) ?>
</div>
