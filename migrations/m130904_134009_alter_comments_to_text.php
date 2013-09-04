<?php

class m130904_134009_alter_comments_to_text extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('et_ophdrprescription_details','comments','TEXT NULL DEFAULT NULL COLLATE \'utf8_bin\'');
	}

	public function down()
	{
		$this->alterColumn('et_ophdrprescription_details','comments','varchar(255)');
	}
}