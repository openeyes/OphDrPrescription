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
class PrescriptionCommonController extends DefaultController
{
	static protected $action_types = array(
		'setForm' => self::ACTION_TYPE_FORM,
		'setFormAdmin' => self::ACTION_TYPE_FORM,
		'itemForm' => self::ACTION_TYPE_FORM,
		'itemFormAdmin' => self::ACTION_TYPE_FORM,
		'saveDrugSetAdmin' => self::ACTION_TYPE_FORM,
	);

	/**
	 * Ajax action to get prescription forms for a drug set
	 *
	 * @param $key
	 * @param $patient_id
	 * @param $set_id
	 */
	public function actionSetForm($key, $patient_id, $set_id)
	{
		$this->initForPatient($patient_id);

		$key = (integer)$key;

		$drug_set_items = DrugSetItem::model()->findAllByAttributes(array('drug_set_id' => $set_id));
		foreach ($drug_set_items as $drug_set_item) {
			$this->renderPrescriptionItem($key, $drug_set_item);
			$key++;
		}
	}

	/**
	 * Ajax function to get drug set for admin page (we do not have patient_id there), and we also load the
	 * name and subspecialty here
	 *
	 * @param $key
	 * @param $set_id
	 * @throws CException
	 */
	public function actionSetFormAdmin($key, $set_id)
	{
		$drugset = DrugSet::model()->findByPk($set_id);
		$returnData = array();

		$returnData["drugsetName"] = $drugset->name;
		$returnData["drugsetSubspecialtyId"] = $drugset->subspecialty_id;
		$returnData["tableRows"] = ""; // the HTML content for the prescription items table

		$key = (integer)$key;
		$drug_set_items = DrugSetItem::model()->findAllByAttributes(array('drug_set_id' => $set_id));

		foreach ($drug_set_items as $drug_set_item) {
			$returnData["tableRows"] .= $this->renderPrescriptionItem($key, $drug_set_item);
			$key++;
		}

		echo json_encode($returnData);
	}

	/**
	 * Ajax action to get the form for single drug
	 *
	 * @param $key
	 * @param $patient_id
	 * @param $drug_id
	 */
	public function actionItemForm($key, $patient_id, $drug_id)
	{
		$this->initForPatient($patient_id);
		$this->renderPrescriptionItem($key, $drug_id);
	}

	/**
	 * Ajax action to get the form for single drug on admin page (we don't have patient_id there)
	 *
	 * @param $key
	 * @param $patient_id
	 * @param $drug_id
	 */
	public function actionItemFormAdmin($key, $drug_id)
	{
		echo $this->renderPrescriptionItem($key, $drug_id);
	}


	/**
	 * Render the form for a OphDrPrescription_Item, DrugSetItem or Drug (by id)
	 *
	 * @param $key
	 * @param OphDrPrescription_Item|DrugSetItem|integer $source
	 * @throws CException
	 */
	protected function renderPrescriptionItem($key, $source)
	{
		$item = new OphDrPrescription_Item();
		if (is_a($source, 'OphDrPrescription_Item')) {

			// Source is a prescription item, so we should clone it
			foreach (array('drug_id', 'duration_id', 'frequency_id', 'dose', 'route_option_id', 'route_id') as $field) {
				$item->$field = $source->$field;
			}
			if ($source->tapers) {
				$tapers = array();
				foreach ($source->tapers as $taper) {
					$taper_model = new OphDrPrescription_ItemTaper();
					$taper_model->dose = $taper->dose;
					$taper_model->frequency_id = $taper->frequency_id;
					$taper_model->duration_id = $taper->duration_id;
					$tapers[] = $taper_model;
				}
				$item->tapers = $tapers;
			}
		} else {

			if (is_a($source, 'DrugSetItem')) {

				// Source is an drug set item which contains frequency and duration data
				$item->drug_id = $source->drug_id;
				$item->loadDefaults();
				foreach (array('duration_id', 'frequency_id', 'dose') as $field) {
					if ($source->$field) {
						$item->$field = $source->$field;
					}
				}
				if ($source->tapers) {
					$tapers = array();
					foreach ($source->tapers as $taper) {
						$taper_model = new OphDrPrescription_ItemTaper();
						foreach (array('duration_id', 'frequency_id', 'dose') as $field) {
							if ($taper->$field) {
								$taper_model->$field = $taper->$field;
							} else {
								$taper_model->$field = $item->$field;
							}
						}
						$tapers[] = $taper_model;
					}
					$item->tapers = $tapers;
				}

			} elseif (is_int($source) || (int)$source) {

				// Source is an integer, so we use it as a drug_id
				$item->drug_id = $source;
				$item->loadDefaults();

			} else {
				throw new CException('Invalid prescription item source: ' . print_r($source));
			}

			// Populate route option from episode for Eye
			if ($episode = $this->episode) {
				if ($principal_eye = $episode->eye) {
					$route_option_id = DrugRouteOption::model()->find('name = :eye_name',
						array(':eye_name' => $principal_eye->name));
					$item->route_option_id = ($route_option_id) ? $route_option_id : null;
				}
				//check operation note eye and use instead of original diagnosis
				if ($api = Yii::app()->moduleAPI->get('OphTrOperationnote')) {
					if ($apieye = $api->getLastEye($this->patient)) {
						$item->route_option_id = $apieye;
					}
				}
			}
		}
		if (isset($this->patient)) {
			$this->renderPartial('/default/form_Element_OphDrPrescription_Details_Item',
				array('key' => $key, 'item' => $item, 'patient' => $this->patient));
		} else {
			$output = $this->renderPartial('/default/form_Element_OphDrPrescription_Details_Item',
				array('key' => $key, 'item' => $item), true);

			return $output;
		}

	}
	
}