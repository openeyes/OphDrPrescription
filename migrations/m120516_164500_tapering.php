<?php

class m120516_164500_tapering extends CDbMigration
{
	public function up()
	{
		// Create tapering table
		$this->createTable('ophdrprescription_item_taper', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'item_id' => 'int(10) unsigned NOT NULL',
				'dose' => 'varchar(40)',
				'frequency_id' => 'int(10) unsigned NOT NULL',
				'duration_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'CONSTRAINT `ophdrprescription_item_taper_item_id_fk` FOREIGN KEY (`item_id`) REFERENCES `ophdrprescription_item` (`id`)',
				'CONSTRAINT `ophdrprescription_item_taper_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`)',
				'CONSTRAINT `ophdrprescription_item_taper_duration_id_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`)',
				'CONSTRAINT `ophdrprescription_item_taper_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophdrprescription_item_taper_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

	}

	public function down()
	{
		$this->dropTable('ophdrprescription_item_taper');
	}

}
