<div class="eventHighlight">
	<div class="grid-view">
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
				<?php foreach($element->items as $key => $item) { ?>
				<tr	class="prescriptionItem<?php if($this->patient->hasAllergy($item->drug_id)) { ?> allergyWarning<?php } ?> <?php echo (($key % 2) == 0) ? 'even' : 'odd'; ?>">
					<td class="prescriptionLabel"><?php echo $item->drug->label; ?></td>
					<td><?php echo $item->dose ?></td>
					<td><?php echo $item->route->name ?><?php if($item->route_option) { echo ' ('.$item->route_option->name.')'; } ?></td>
					<td><?php echo $item->frequency->name ?></td>
					<td><?php echo $item->duration->name ?></td>
				</tr>
				<?php foreach($item->tapers as $taper) { ?>
				<tr class="prescriptionTaper <?php echo (($key % 2) == 0) ? 'even' : 'odd'; ?>">
					<td class="prescriptionLabel"><span>then</span></td>
					<td><?php echo $taper->dose ?></td>
					<td></td>
					<td><?php echo $taper->frequency->name ?></td>
					<td><?php echo $taper->duration->name ?></td>
				</tr>
				<?php	} } ?>
			</tbody>
		</table>
	</div>
</div>

<?php if($element->comments) { ?>
<h4>
	<?php echo CHtml::encode($element->getAttributeLabel('comments'))?>
</h4>
<div class="eventHighlight comments">
	<h4>
		<?php echo CHtml::encode($element->comments)?>
	</h4>
</div>
<?php } ?>
