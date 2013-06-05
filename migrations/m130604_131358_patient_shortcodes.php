<?php

class m130604_131358_patient_shortcodes extends CDbMigration
{
	public function up()
	{
		$event_type = EventType::model()->find('class_name=?',array('OphDrPrescription'));

		$event_type->registerShortcode('pre','getLetterPrescription','Prescription');
	}

	public function down()
	{
		$event_type = EventType::model()->find('class_name=?',array('OphDrPrescription'));

		$this->delete('patient_shortcode','event_type_id=:etid',array(':etid'=>$event_type->id));
	}
}
