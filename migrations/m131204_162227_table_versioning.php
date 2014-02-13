<?php

class m131204_162227_table_versioning extends CDbMigration
{
	public function up()
	{
		$this->execute("
CREATE TABLE `et_ophdrprescription_details_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`comments` text DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`printed` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`draft` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`print` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_et_ophdrprescription_details_event_id_fk` (`event_id`),
	KEY `acv_et_ophdrprescription_details_created_user_id_fk` (`created_user_id`),
	KEY `acv_et_ophdrprescription_details_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_et_ophdrprescription_details_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophdrprescription_details_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
	CONSTRAINT `acv_et_ophdrprescription_details_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('et_ophdrprescription_details_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','et_ophdrprescription_details_version');

		$this->createIndex('et_ophdrprescription_details_aid_fk','et_ophdrprescription_details_version','id');

		$this->addColumn('et_ophdrprescription_details_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('et_ophdrprescription_details_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','et_ophdrprescription_details_version','version_id');
		$this->alterColumn('et_ophdrprescription_details_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophdrprescription_item_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`prescription_id` int(10) unsigned NOT NULL,
	`drug_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`dose` varchar(40) DEFAULT NULL,
	`route_id` int(10) unsigned NOT NULL,
	`frequency_id` int(10) unsigned NOT NULL,
	`duration_id` int(10) unsigned NOT NULL,
	`route_option_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_ophdrprescription_details_prescription_id_fk` (`prescription_id`),
	KEY `acv_ophdrprescription_details_drug_id_fk` (`drug_id`),
	KEY `acv_ophdrprescription_details_created_user_id_fk` (`created_user_id`),
	KEY `acv_ophdrprescription_details_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_ophdrprescription_item_route_option_id_fk` (`route_option_id`),
	KEY `acv_ophdrprescription_item_route_id_fk` (`route_id`),
	KEY `acv_ophdrprescription_item_frequency_id_fk` (`frequency_id`),
	KEY `acv_ophdrprescription_item_duration_id_fk` (`duration_id`),
	CONSTRAINT `acv_ophdrprescription_item_duration_id_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
	CONSTRAINT `acv_ophdrprescription_details_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophdrprescription_details_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
	CONSTRAINT `acv_ophdrprescription_details_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophdrprescription_details_prescription_id_fk` FOREIGN KEY (`prescription_id`) REFERENCES `et_ophdrprescription_details` (`id`),
	CONSTRAINT `acv_ophdrprescription_item_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
	CONSTRAINT `acv_ophdrprescription_item_route_id_fk` FOREIGN KEY (`route_id`) REFERENCES `drug_route` (`id`),
	CONSTRAINT `acv_ophdrprescription_item_route_option_id_fk` FOREIGN KEY (`route_option_id`) REFERENCES `drug_route_option` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophdrprescription_item_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophdrprescription_item_version');

		$this->createIndex('ophdrprescription_item_aid_fk','ophdrprescription_item_version','id');

		$this->addColumn('ophdrprescription_item_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophdrprescription_item_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophdrprescription_item_version','version_id');
		$this->alterColumn('ophdrprescription_item_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophdrprescription_item_taper_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`item_id` int(10) unsigned NOT NULL,
	`dose` varchar(40) DEFAULT NULL,
	`frequency_id` int(10) unsigned NOT NULL,
	`duration_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophdrprescription_item_taper_item_id_fk` (`item_id`),
	KEY `acv_ophdrprescription_item_taper_frequency_id_fk` (`frequency_id`),
	KEY `acv_ophdrprescription_item_taper_duration_id_fk` (`duration_id`),
	KEY `acv_ophdrprescription_item_taper_created_user_id_fk` (`created_user_id`),
	KEY `acv_ophdrprescription_item_taper_last_modified_user_id_fk` (`last_modified_user_id`),
	CONSTRAINT `acv_ophdrprescription_item_taper_item_id_fk` FOREIGN KEY (`item_id`) REFERENCES `ophdrprescription_item` (`id`),
	CONSTRAINT `acv_ophdrprescription_item_taper_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
	CONSTRAINT `acv_ophdrprescription_item_taper_duration_id_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
	CONSTRAINT `acv_ophdrprescription_item_taper_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophdrprescription_item_taper_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophdrprescription_item_taper_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophdrprescription_item_taper_version');

		$this->createIndex('ophdrprescription_item_taper_aid_fk','ophdrprescription_item_taper_version','id');

		$this->addColumn('ophdrprescription_item_taper_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophdrprescription_item_taper_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophdrprescription_item_taper_version','version_id');
		$this->alterColumn('ophdrprescription_item_taper_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->addColumn('et_ophdrprescription_details','deleted','tinyint(1) unsigned not null');
		$this->addColumn('et_ophdrprescription_details_version','deleted','tinyint(1) unsigned not null');

		$this->addColumn('ophdrprescription_item','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophdrprescription_item_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophdrprescription_item_taper','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophdrprescription_item_taper_version','deleted','tinyint(1) unsigned not null');
	}

	public function down()
	{
		$this->dropTable('et_ophdrprescription_details_version');
		$this->dropTable('ophdrprescription_item_version');
		$this->dropTable('ophdrprescription_item_taper_version');
	}
}
