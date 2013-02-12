<?php $this->header() ?>

<h3 class="withEventIcon"><?php echo $this->event_type->name ?></h3>

<?php $this->renderPartial('//base/_messages'); ?>

<div id="event_<?php echo $this->module->name?>">
	<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'clinical-create',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('class'=>'sliding'),
		));

		// Event actions
		$this->event_actions[] = EventAction::button('Save draft', 'savedraft', array('id' => 'et_save_draft', 'colour' => 'green'));
		$this->event_actions[] = EventAction::button('Save and print', 'saveprint', array('id' => 'et_save_print', 'colour' => 'green'));
		$this->renderPartial('//patient/event_actions');
	?>

		<?php $this->displayErrors($errors)?>

		<div class="elements">
			<?php $this->renderDefaultElements($this->action->id, $form); ?>
			<?php $this->renderOptionalElements($this->action->id, $form); ?>
		</div>

		<?php $this->displayErrors($errors)?>

		<div class="cleartall"></div>
	<?php $this->endWidget(); ?>
</div>

<?php $this->footer() ?>
