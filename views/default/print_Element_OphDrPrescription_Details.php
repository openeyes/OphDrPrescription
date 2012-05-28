<?php foreach(array('','Copy for notes','Copy for patient') as $copy) { ?>
<?php if($copy) {?>
<div class="pageBreak"></div>
<?php } ?>
<div class="banner">
	<div class="seal">
		<img src="/img/_print/letterhead_seal.jpg" alt="letterhead_seal" />
	</div>
	<div class="logo">
		<img src="/img/_print/letterhead_Moorfields_NHS.jpg"
			alt="letterhead_Moorfields_NHS" />
	</div>
	<?php if($copy) { ?>
	<div class="watermark">
		<?php echo $copy; ?>
	</div>
	<?php } ?>
</div>
<div class="fromAddress">
	<?php echo $this->site->letterhtml ?>
	<br />Tel:
	<?php echo CHtml::encode($this->site->telephone) ?>
	<?php if($this->site->fax) { ?>
	<br />Fax:
	<?php echo CHtml::encode($this->site->fax) ?>
	<?php } ?>
</div>
<h1>Prescription Form</h1>

<?php
$firm = $element->event->episode->firm;
if($consultant = $firm->getConsultant()) {
	$consultantName = $consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name;
} else {
	$consultantName = 'CONSULTANT';
}
$subspecialty = $firm->serviceSubspecialtyAssignment->subspecialty;
?>
<table id="prescription_header">
	<tr>
		<th>Patient Name</th>
		<td><?php echo $this->patient->fullname ?> (<?php echo $this->patient->gender ?>)</td>
		<th>Hospital Number</th>
		<td><?php echo $this->patient->hos_num ?></td>
	</tr>
	<tr>
		<th>Date of Birth</th>
		<td><?php echo $this->patient->NHSDate('dob') ?> (<?php echo $this->patient->age ?>)</td>
		<th>NHS Number</th>
		<td><?php echo $this->patient->nhs_num ?></td>
	</tr>
	<tr>
		<th>Consultant</th>
		<td><?php echo $consultantName ?></td>
		<th>Service</th>
		<td><?php echo $subspecialty->name ?></td>
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

<p>Trust policy limits supply to a maximum of 2 weeks</p>

<h2>Allergies</h2>
<p class="box">
	<?php echo $this->patient->getAllergiesString() ?>
</p>

<h2>Comments</h2>
<p class="box">
	<?php echo $element->comments?>
</p>

<h2>Pharmacy Use Only</h2>
<table id="pharmacy_checkboxes">
	<tr>
		<th>Used medication before?</th>
		<td>Yes &#10063; / No &#10063;</td>
		<th>Allergies</th>
		<td>Yes &#10063; / No &#10063;</td>
	</tr>
	<tr>
		<th>Heart problems</th>
		<td>Yes &#10063; / No &#10063;</td>
		<th>Respiritory problems</th>
		<td>Yes &#10063; / No &#10063;</td>
	</tr>
	<tr>
		<th>Drug history</th>
		<td>Yes &#10063; / No &#10063;</td>
		<th>Continued from GP?</th>
		<td>Yes &#10063; / No &#10063;</td>
	</tr>
</table>
<table id="done_bys">
	<tr>
		<th>Prescribed by</th>
		<td><?php echo $element->user->fullname ?>
		</td>
		<th>Date</th>
		<td><?php echo $element->NHSDate('last_modified_date') ?>
		</td>
	</tr>
	<tr class="handWritten">
		<th>Dispensed by</th>
		<td></td>
		<th>Date</th>
		<td></td>
	</tr>
	<tr class="handWritten">
		<th>Checked by</th>
		<td></td>
		<th>Date</th>
		<td></td>
	</tr>
</table>

<?php if(!$copy) { ?>
<p>Doctor's Signature:</p>
<?php } ?>
<?php } ?>