<?php

class m120529_142100_add_draft_and_locking extends CDbMigration {

	public function up() {

		$this->addColumn('et_ophdrprescription_details', 'printed', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('et_ophdrprescription_details', 'locked', 'tinyint(1) unsigned NOT NULL DEFAULT 0');

	}

	public function down() {
		$this->dropColumn('et_ophdrprescription_details', 'printed');
		$this->dropColumn('et_ophdrprescription_details', 'locked');
	}

}