<?php

class m120515_174700_prescription_item_route_options extends CDbMigration {

	public function up() {

		// Add extra metadata to prescription items
		$this->addColumn('ophdrprescription_item', 'route_option_id', 'int(10) unsigned');
		$this->addForeignKey('ophdrprescription_item_route_option_id_fk', 'ophdrprescription_item','route_option_id', 'drug_route_option', 'id');

		// Add foreign keys
		$this->addForeignKey('ophdrprescription_item_route_id_fk', 'ophdrprescription_item','route_id', 'drug_route', 'id');
		$this->addForeignKey('ophdrprescription_item_frequency_id_fk', 'ophdrprescription_item','frequency_id', 'drug_frequency', 'id');
		$this->addForeignKey('ophdrprescription_item_duration_id_fk', 'ophdrprescription_item','duration_id', 'drug_duration', 'id');
		
	}

	public function down() {
		$this->dropForeignKey('ophdrprescription_item_route_option_id_fk', 'ophdrprescription_item');
		$this->dropColumn('ophdrprescription_item', 'route_option_id');
		
		$this->dropForeignKey('ophdrprescription_item_route_id_fk', 'ophdrprescription_item');
		$this->dropForeignKey('ophdrprescription_item_frequency_id_fk', 'ophdrprescription_item');
		$this->dropForeignKey('ophdrprescription_item_duration_id_fk', 'ophdrprescription_item');
	}

}