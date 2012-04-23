<?php

class m120423_114231_initial_migration_for_ophdrprescription extends CDbMigration {

	public function up() {

		// Get the event group for ‘Drug events’
		/* Currently done in core migration
		 * 
		$group = $this->dbConnection->createCommand()
		->select('id')
		->from('event_group')
		->where('name=:name',array(':name'=>'Drug events'))
		->queryRow();

		// Create the new Prescription event_type
		$this->insert('event_type', array(
				'name' => 'Prescription',
				'event_group_id' => $group['id'],
				'class_name' => 'OphDrPrescription'
		));
		 */

		// Get the newly created event type
		$event_type = $this->dbConnection->createCommand()
		->select('id')
		->from('event_type')
		->where('name=:name', array(':name'=>'Prescription'))
		->queryRow();

		// Create an element for the new event type called ElementDetails
		$this->insert('element_type', array(
				'name' => 'Details',
				'class_name' => 'ElementDetails',
				'event_type_id' => $event_type['id'],
				'display_order' => 1,
				'default' => 1,
		));

		// Create a table to store the ElementDetails element
		$this->createTable('et_ophdrprescription_details', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(255)',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophdrprescription_details_event_id_fk` (`event_id`)',
				'KEY `et_ophdrprescription_details_created_user_id_fk` (`created_user_id`)',
				'KEY `et_ophdrprescription_details_last_modified_user_id_fk` (`last_modified_user_id`)',
				'CONSTRAINT `et_ophdrprescription_details_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_ophdrprescription_details_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophdrprescription_details_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

	}

	public function down() {
		
		// Drop the table created for ElementDetails
		$this->dropTable('et_ophdrprescription_details');

		// Find the event type
		$event_type = $this->dbConnection->createCommand()
		->select('id')
		->from('event_type')
		->where('name=:name', array(':name'=>'Prescription'))
		->queryRow();

		// Find the ElementDetails element type
		$element_type = $this->dbConnection->createCommand()
		->select('id')
		->from('element_type')
		->where('name=:name and event_type_id=:event_type_id',array(
				':name'=>'Details',
				':event_type_id'=>$event_type['id']
		))->queryRow();

		// Delete the ElementDetails element type
		$this->delete('element_type','id='.$element_type['id']);

		/* Currently done in core migration
		 *
		// Delete the event type
		$this->delete('event_type','id='.$event_type['id']);
		 */

	}

}