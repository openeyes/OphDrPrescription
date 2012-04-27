<div class="<?php echo $element->elementType->class_name?>">
	<h4 class="elementTypeName">
		<?php echo $element->elementType->name ?>
	</h4>
	<div id="div_Element_OphDrPrescription_Details_prescription_items"
		class="eventDetail">
		<div class="label">Items</div>
		<?php echo CHtml::dropDownList('drug_id', null, $element->getCommonDrugList(), array('empty' => 'Select a drug')); ?>
		<ul id="prescription_items">
			<?php foreach($element->items as $item) { ?>
			<li>
				<?php echo $item->drug->name; ?>
				<input type="hidden" name="prescription_item[]" value="<?php echo $item->id?>">
				<a href="#">Remove</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php echo $form->textArea($element, 'comments', array('rows' => 4, 'cols' => 60)) ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#drug_id').unbind('change').bind('change',function() {
		var selected = $(this).children('option:selected');
		if (selected.val().length) {
			var new_item = selected.text() + ' <a href="#">Remove</a>';
			new_item += '<input type="hidden" name="prescription_item[]" value="'+selected.val()+'">';
			$('#prescription_items').append('<li>' + new_item + '</li>');
			selected.remove();
			$(this).val('');
		}
		return false;
	});
});
</script>

<!-- 
(function($){

    // Initialise multiselect field
    multiSelectFieldInitialise = function(){
        var multiSelectField = $(this);
        var sourceField = $('select', multiSelectField);
        
        // Add source, destination and control fields
        sourceField.addClass('hidden');
        sourceField.before('<select class="multiselect-unselected" multiple="multiple"></select>');
        sourceField.before('<div class="multiselect-controls"><p><button class="action multiselect-add">Add &gt;</button></p><p><button class="action multiselect-remove">&lt; Remove</button></p></div>');
        sourceField.before('<select class="multiselect-selected" multiple="multiple"></select>');
        
        // Move unselected items to source copy selected items to dest
        var selectedField = $('.multiselect-selected', multiSelectField);
        var unselectedField = $('.multiselect-unselected', multiSelectField);
        $('option:not(:selected)', sourceField).appendTo(unselectedField);
        $('option:selected', sourceField).clone().appendTo(selectedField).attr('selected', '');
        
        // Configure controls
        $('.multiselect-add', multiSelectField).click(function(){
            $('option:selected', unselectedField).appendTo(selectedField).attr('selected', '');
            $('option', sourceField).remove();
            sourceField.append($('option', selectedField).clone());
            $('option', sourceField).attr('selected', 'selected');
            return false;
        });
        $('.multiselect-remove', multiSelectField).click(function(){
            $('option:selected', selectedField).appendTo(unselectedField).attr('selected', '');
            $('option', sourceField).remove();
            sourceField.append($('option', selectedField).clone());
            $('option', sourceField).attr('selected', 'selected');
            return false;
        });
    }
    
    if (typeof $(document).livequery != 'undefined') {
        $('.multiselect').livequery(multiSelectFieldInitialise);
    }
    else {
        $(document).ready(function(){
            $('.multiselect').each(multiSelectFieldInitialise);
        });
    }
    
})(jQuery);

 -->
