<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php
$element = Element_OphDrPrescription_Details::model();
?>

<div class="box admin">
	<h2>Edit Drug Sets</h2>

	<div class="row field-row">
		<div class="large-4 column"><h3>Select a set:</h3></div>
	</div>
	<div class="row field-row">
		<div class="large-2 column"><label for="set_name">Saved sets:</label></div>
		<div class="large-4 column">
			<?php echo CHtml::dropDownList('drug_set_id', null, CHtml::listData($element->drugSetsAll(), 'id', 'name'),
				array('empty' => '-- Select this to add new --')); ?>
		</div>
		<div class="large-6 column end"></div>
	</div>

	<div class="row field-row">
		<div class="large-4 column"><h3>OR Add new:</h3></div>
	</div>
	<div class="row field-row">
		<div class="large-2 column"><label for="set_name">Set name:</label></div>
		<div class="large-4 column">
			<?php echo CHtml::textField('set_name'); ?>
		</div>
		<div class="large-2 column"><label for="site_id">Subspeciality:</label></div>
		<div class="large-4 column end">
			<?php
			// $selectedsubspecialty
			echo CHtml::dropDownList('subspecialty_id', "",
				CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'), array("empty" => "-- Select --"));
			?>
		</div>
	</div>


	<section class="element">
		<?php
		$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id' => 'prescription-create',
			'enableAjaxValidation' => false,
		));

		//$this->displayErrors($errors)
		?>

		<?php

		$this->renderPartial("/default/form_Element_OphDrPrescription_Details",
			array("form" => $form, "element" => $element));

		//$this->displayErrors($errors, true);
		$this->endWidget();
		?>
	</section>
	<div class="box-header">
		<div class="box-actions">
			<button type="button" class="small"
					id="save_set_data" name="save_set_data">
				Save this set
			</button>
		</div>
	</div>
</div>
