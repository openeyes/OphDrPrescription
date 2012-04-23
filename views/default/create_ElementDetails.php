<div class="<?php echo $element->elementType->class_name?>">
	<h4 class="elementTypeName">
		<?php echo $element->elementType->name ?>
	</h4>
	<?php echo $form->textArea($element, 'comments', array('rows' => 4, 'cols' => 60))?>
</div>
