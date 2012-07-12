<?php

class m120712_074517_added_draft_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophdrprescription_details','draft','tinyint(1) unsigned NOT NULL DEFAULT 1');
	}

	public function down()
	{
		$this->dropColumn('et_ophdrprescription_details','draft');
	}
}
