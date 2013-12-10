<?php

class m131210_144533_soft_deletion extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophdrprescription_item','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophdrprescription_item_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophdrprescription_item_taper','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophdrprescription_item_taper_version','deleted','tinyint(1) unsigned not null');
	}

	public function down()
	{
		$this->dropColumn('ophdrprescription_item','deleted');
		$this->dropColumn('ophdrprescription_item_version','deleted');
		$this->dropColumn('ophdrprescription_item_taper','deleted');
		$this->dropColumn('ophdrprescription_item_taper_version','deleted');
	}
}
