<div class="watermark">
	<div>Copy for notes</div>
</div>
<div class="banner">
	<div class="seal"><img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" /></div>
	<div class="logo"><img src="/img/_print/letterhead_Moorfields_NHS.jpg" alt="letterhead_Moorfields_NHS" /></div>
</div>
<div class="fromAddress">
	<?php echo $this->site->letterhtml ?>
	<br />Tel: <?php echo CHtml::encode($this->site->telephone) ?>
	<?php if($this->site->fax) { ?>
	<br />Fax: <?php echo CHtml::encode($this->site->fax) ?>
	<?php } ?>
</div>
<?php
	$firm = $element->event->episode->firm;
	if($consultant = $firm->getConsultant()) {
		$consultantName = $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
	} else {
		$consultantName = 'CONSULTANT';
	}
	$subspecialty = $firm->serviceSubspecialtyAssignment->subspecialty;
?>
<table>
	<tr>
		<td>Patient Name: <?php echo $this->patient->fullname ?>
		</td>
		<td>Hospital Number: <?php echo $this->patient->hos_num ?>
		</td>
	</tr>
	<tr>
		<td>Date of Birth: <?php echo $this->patient->dob ?> (<?php echo $this->patient->age ?>)
		</td>
		<td>NHS Number: <?php echo $this->patient->nhs_num ?>
		</td>
	</tr>
	<tr>
		<td>Consultant: <?php echo $consultantName ?>
		</td>
		<td>Service: <?php echo $subspecialty->name ?>
		</td>
	</tr>
	<tr>
		<td>Prescribed by: <?php echo $element->user->fullname ?>
		</td>
		<td>Bleep Number: FIXME
		</td>
	</tr>
</table>
<table id="prescription_items">
	<thead>
		<tr>
			<th>Prescription details</th>
			<th>Dose</th>
			<th>Route</th>
			<th>Frequency</th>
			<th>Duration</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($element->items as $key => $item) { ?>
		<tr
			class="prescriptionItem<?php if($this->patient->hasAllergy($item->drug_id)) { ?> allergyWarning<?php } ?>">
			<td class="prescriptionLabel"><?php echo $item->drug->label; ?></td>
			<td><?php echo $item->dose ?></td>
			<td><?php echo $item->route->name ?> <?php if($item->route_option) { 
				echo ' ('.$item->route_option->name.')';
			} ?></td>
			<td><?php echo $item->frequency->name ?></td>
			<td><?php echo $item->duration->name ?></td>
		</tr>
		<?php foreach($item->tapers as $taper) { ?>
		<tr class="prescriptionTaper">
			<td class="prescriptionLabel">then</td>
			<td><?php echo $taper->dose ?></td>
			<td></td>
			<td><?php echo $taper->frequency->name ?></td>
			<td><?php echo $taper->duration->name ?></td>
		</tr>
		<?php	} 
} ?>
	</tbody>
</table>

<p>Trust policy limits supply to a maximum of 4 weeks</p>

<h4>Comments</h4>
<p>
	<?php echo $element->comments?>
</p>
