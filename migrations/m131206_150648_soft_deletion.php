<?php

class m131206_150648_soft_deletion extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophdrprescription_details','deleted','tinyint(1) unsigned not null');
		$this->addColumn('et_ophdrprescription_details_version','deleted','tinyint(1) unsigned not null');
	}

	public function down()
	{
		$this->dropColumn('et_ophdrprescription_details','deleted');
		$this->dropColumn('et_ophdrprescription_details_version','deleted');
	}
}
