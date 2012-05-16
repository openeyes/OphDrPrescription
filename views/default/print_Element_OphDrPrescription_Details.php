<h4>Drugs</h4>
<div class="eventHighlight">
		<ul>
			<?php foreach($element->items as $item) { ?>
			<li<?php if($this->patient->hasAllergy($item->drug->id)) { ?> class="allergyWarning"<?php } ?>><?php echo $item->description ?></li>
			<?php } ?>
		</ul>
</div>
