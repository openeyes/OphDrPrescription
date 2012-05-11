<?php

class m120510_152211_add_metadata_to_prescription_items extends CDbMigration {

	public function up() {

		// Add extra metadata to prescription items
		$this->addColumn('ophdrprescription_item', 'dose', 'varchar(40)');
		$this->addColumn('ophdrprescription_item', 'route_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('ophdrprescription_item', 'frequency_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('ophdrprescription_item', 'duration_id', 'int(10) unsigned NOT NULL');

	}

	public function down() {
		$this->dropColumn('ophdrprescription_item', 'dose');
		$this->dropColumn('ophdrprescription_item', 'route_id');
		$this->dropColumn('ophdrprescription_item', 'frequency_id');
		$this->dropColumn('ophdrprescription_item', 'duration_id');
	}

}