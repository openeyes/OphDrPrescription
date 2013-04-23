<?php

class m130423_141157_print_flag extends CDbMigration
{
	public function up()
	{
		$this->addColumn('et_ophdrprescription_details','print','tinyint(1) unsigned NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('et_ophdrprescription_details','print');
	}
}
