<?php $this->header() ?>

<h3 class="withEventIcon"><?php echo $this->event_type->name ?></h3>

<?php
	// Event actions
	$this->event_actions[] = EventAction::button('Print', 'print', array('id' => 'et_print'));
	$this->renderPartial('//patient/event_actions');
?>

<?php $this->renderPartial('//base/_messages'); ?>

<?php if (Element_OphDrPrescription_Details::model()->find('event_id=?',array($this->event->id))->draft) {?>
	<div class="alertBox">
		This prescription is a draft and can still be edited
	</div>
<?php }?>

<div>
	<?php $this->renderDefaultElements($this->action->id); ?>
	<?php $this->renderOptionalElements($this->action->id); ?>
	<div class="cleartall"></div>
</div>

<div class="metaData">
	<span class="info"> Prescription created by <span class="user"><?php echo $this->event->user->fullname ?>
	</span> on <?php echo $this->event->NHSDate('created_date') ?> at
		<?php echo date('H:i', strtotime($this->event->created_date)) ?>
	</span> <span class="info"> Prescription last modified by <span
		class="user"><?php echo $this->event->usermodified->fullname ?>
	</span> on <?php echo $this->event->NHSDate('last_modified_date') ?>
		at <?php echo date('H:i', strtotime($this->event->last_modified_date)) ?>
	</span>
</div>

<script type="text/javascript">
	var module_css_path = "<?php echo $this->assetPath.'/css'?>";
	<?php if(isset(Yii::app()->session['print_prescription'])) {
		unset(Yii::app()->session['print_prescription']); ?>
	$(document).ready(function() {
		do_print_prescription();
	});
	<?php } ?>
</script>

<?php $this->footer() ?>
