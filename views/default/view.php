<?php
	$this->breadcrumbs=array($this->module->id);
	$this->editable = BaseController::checkUserLevel(4);
	$this->header();
?>

<h3 class="withEventIcon" style="background:transparent url(<?php echo $this->assetPath?>/img/medium.png) center left no-repeat;"><?php echo $this->event_type->name ?></h3>

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

	<div class="form_button">
	<img class="loader" style="display: none;" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." />
	<button type="submit" class="classy blue venti" id="et_print" name="print"><span class="button-span button-span-blue">Print</span></button>
	<script type="text/javascript">
		var module_css_path = "<?php echo $this->assetPath.'/css'?>";
		<?php if(isset(Yii::app()->session['print_prescription'])) {
			unset(Yii::app()->session['print_prescription']); ?>
		$(document).ready(function() {
			do_print_prescription();
		});
		<?php } ?>
	</script>
</div>

<?php $this->footer() ?>
