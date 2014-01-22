<?php
/**
 * OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

class DefaultController extends BaseEventTypeController
{
	public function accessRules()
	{
		return array(
			// Level 2 can't change anything
			array('allow',
				'actions' => array('view'),
				'expression' => 'BaseController::checkUserLevel(2)',
			),
			// Level 3 can print
			array('allow',
				'actions' => $this->printActions(),
				'expression' => 'BaseController::checkUserLevel(3)',
			),
			// Level 5 or above can do anything
			array('allow',
				'expression' => 'BaseController::checkUserLevel(5)',
			),
			// Deny anything else (default rule allows authenticated users)
			array('deny'),
		);
	}

	protected function setCommonDrugMetadata()
	{
		$this->jsVars['common_drug_metadata'] = array();
		foreach (Element_OphDrPrescription_Details::model()->commonDrugs() as $drug) {
			$this->jsVars['common_drug_metadata'][$drug->id] = array(
					'type_id' => $drug->type_id,
					'preservative_free' => $drug->preservative_free,
			);
		}
	}

	public function canPrint()
	{
		if (!parent::canPrint()) {
			return false;
		}

		return BaseController::checkUserLevel(3);
	}

	public function printActions()
	{
		return array('print', 'markPrinted');
	}

	public function actionCreate()
	{
		if (!$patient = Patient::model()->findByPk($_REQUEST['patient_id'])) {
			throw new CHttpException(403, 'Invalid patient_id.');
		}

		$this->setCommonDrugMetadata();

		$this->showAllergyWarning($patient);

		// Save and print clicked, stash print flag
		if (isset($_POST['saveprint'])) {
			Yii::app()->session['print_prescription'] = true;
		}

		parent::actionCreate();
	}

	public function actionUpdate($id)
	{
		if (!$event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		$this->setCommonDrugMetadata();

		$this->showAllergyWarning($event->episode->patient);

		// Save and print clicked, stash print flag
		if (isset($_POST['saveprint'])) {
			Yii::app()->session['print_prescription'] = true;
		}

		parent::actionUpdate($id);
	}

	public function actionView($id)
	{
		if (!$event = Event::model()->findByPk($id)) {
			throw new CHttpException(403, 'Invalid event id.');
		}

		// Clear any stale warning
		Yii::app()->user->getFlash('warning.prescription_allergy');

		// Get prescription details element
		$element = Element_OphDrPrescription_Details::model()->findByAttributes(array('event_id' => $event->id));
		$patient = $event->episode->patient;
		foreach ($element->items as $item) {
			if ($patient->hasDrugAllergy($item->drug_id)) {
				$this->showAllergyWarning($event->episode->patient);
				break;
			}
		}
		// set required js variables
		$cs = Yii::app()->getClientScript();
		$cs->registerScript('scr_prescription_view',
				"prescription_print_url = '" . Yii::app()->createUrl('/OphDrPrescription/default/print/'.$id) . "';\n", CClientScript::POS_READY);

		// Prescriptions can only be edited by level 4
		if (!self::checkUserLevel(5)) {
			$this->editable = false;
		}

		parent::actionView($id);
	}

	public function printInit($id)
	{
		parent::printInit($id);
		if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?',array($id))) {
			throw new Exception('Prescription not found: '.$id);
		}
		$prescription->printed = 1;
		if (!$prescription->save()) {
			throw new Exception('Unable to save prescription: '.print_r($prescription->getErrors(),true));
		}
		$this->event->info = $prescription->infotext;
		if (!$this->event->save()) {
			throw new Exception('Unable to save event: '.print_r($this->event->getErrors(),true));
		}
	}

	protected function printHTML($id, $elements, $template='print')
	{
		$this->printPDF($id, $elements);
	}

	protected function printPDF($id, $elements, $template='print', $params=array())
	{
		Yii::app()->getClientScript()->reset();
		$this->layout = '//layouts/pdf';
		$pdf_print = new OEPDFPrint('Openeyes', 'PDF', 'PDF');
		$address = $this->site->getLetterAddress(array(
				'delimiter' => "\n",
				'include_telephone' => true,
				'include_fax' => true,
			));

		foreach(array(
				false => false,
				'notes' => 'Copy for notes',
				'patient' => 'Copy for patient',
			) as $copy => $copy_text) {
			$oeletter = new OELetter(null,$address);
			$oeletter->setBarcode('E:'.$id);
			$oeletter->setHideDate(true);
			$oeletter->setFont('helvetica','10');
			$body = $this->render('print', array(
					'elements' => $elements,
					'eventId' => $id,
					'copy' => $copy,
			), true);
			$oeletter->addBody($body);
			if ($copy_text) {
				$oeletter->setWatermark($copy_text);
			}
			$pdf_print->addLetter($oeletter);
		}
		$pdf_print->output();
	}

	/**
	 * set flash message for ptient allergies
	 *
	 * @param Patient $patient
	 */
	protected function showAllergyWarning($patient)
	{
		if ($patient->no_allergies_date) {
			Yii::app()->user->setFlash('info.prescription_allergy', $patient->getAllergiesString());
		}
		else {
			Yii::app()->user->setFlash('warning.prescription_allergy', $patient->getAllergiesString());
		}
	}

	public function updateElements($elements, $data, $event)
	{
		// TODO: Move model aftersave stuff in here
		return parent::updateElements($elements, $data, $event);
	}

	public function actionDrugList()
	{
		if (Yii::app()->request->isAjaxRequest) {
			$criteria = new CDbCriteria();
			if (isset($_GET['term']) && strlen($term = $_GET['term']) >0) {
				$criteria->addCondition(array('LOWER(name) LIKE :term', 'LOWER(aliases) LIKE :term'), 'OR');
				$params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
			}
			if (isset($_GET['type_id']) && $type_id = $_GET['type_id']) {
				$criteria->addCondition('type_id = :type_id');
				$params[':type_id'] = $type_id;
			}
			if (isset($_GET['preservative_free']) && $preservative_free = $_GET['preservative_free']) {
				$criteria->addCondition('preservative_free = 1');
			}
			$criteria->order = 'name';
			$criteria->params = $params;
			$drugs = Drug::model()->findAll($criteria);
			$return = array();
			foreach ($drugs as $drug) {
				$return[] = array(
						'label' => $drug->tallmanlabel,
						'value' => $drug->tallman,
						'id' => $drug->id,
				);
			}
			echo CJSON::encode($return);
		}
	}

	public function actionRepeatForm($key, $patient_id, $current_id = null)
	{
		$patient = Patient::model()->findByPk($patient_id);
		if ($prescription = $this->getPreviousPrescription($patient, $current_id)) {
			foreach ($prescription->items as $item) {
				$this->renderPrescriptionItem($key, $patient, $item);
				$key++;
			}
		}
	}

	public function getPreviousPrescription($patient, $current_id = null)
	{
		$episode = $patient->getEpisodeForCurrentSubspecialty();
		if ($episode) {
			$condition = 'episode_id = :episode_id';
			$params = array(':episode_id' => $episode->id);
			if ($current_id) {
				$condition .= ' AND t.id != :current_id';
				$params[':current_id'] = $current_id;
			}
			$condition .= ' AND event.deleted = 0';
			return Element_OphDrPrescription_Details::model()->find(array(
					'condition' => $condition,
					'join' => 'JOIN event ON event.id = t.event_id',
					'order' => 'created_date DESC',
					'params' => $params,
			));
		}
	}

	public function actionSetForm($key, $patient_id, $set_id)
	{
		$patient = Patient::model()->findByPk($patient_id);
		$drug_set_items = DrugSetItem::model()->findAllByAttributes(array('drug_set_id' => $set_id));
		foreach ($drug_set_items as $drug_set_item) {
			$this->renderPrescriptionItem($key, $patient, $drug_set_item);
			$key++;
		}
	}

	public function actionItemForm($key, $patient_id, $drug_id)
	{
		$patient = Patient::model()->findByPk($patient_id);
		$this->renderPrescriptionItem($key, $patient, $drug_id);
	}

	protected function renderPrescriptionItem($key, $patient, $source)
	{
		$item = new OphDrPrescription_Item();
		if (is_a($source,'OphDrPrescription_Item')) {

			// Source is a prescription item, so we should clone it
			foreach (array('drug_id','duration_id','frequency_id','dose','route_option_id','route_id') as $field) {
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

			if (is_a($source,'DrugSetItem')) {

				// Source is an drug set item which contains frequency and duration data
				$item->drug_id = $source->drug_id;
				$item->loadDefaults();
				foreach (array('duration_id','frequency_id','dose') as $field) {
					if ($source->$field) {
						$item->$field = $source->$field;
					}
				}
				if ($source->tapers) {
					$tapers = array();
					foreach ($source->tapers as $taper) {
						$taper_model = new OphDrPrescription_ItemTaper();
						foreach (array('duration_id','frequency_id','dose') as $field) {
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

			} elseif (is_int($source) || (int) $source) {

				// Source is an integer, so we use it as a drug_id
				$item->drug_id = $source;
				$item->loadDefaults();

			} else {
				throw new CException('Invalid prescription item source: '.print_r($source));
			}

			// Populate route option from episode for Eye
			if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
				if ($principal_eye = $episode->eye) {
					$route_option_id = DrugRouteOption::model()->find('name = :eye_name', array(':eye_name' => $principal_eye->name));
					$item->route_option_id = ($route_option_id) ? $route_option_id : null;
				}
			}
		}
		$this->renderPartial('form_Element_OphDrPrescription_Details_Item', array('key' => $key, 'item' => $item, 'patient' => $patient));
	}

	public function actionRouteOptions($key, $route_id)
	{
		$options = DrugRouteOption::model()->findAllByAttributes(array('drug_route_id' => $route_id));
		if ($options) {
			echo CHtml::dropDownList('prescription_item['.$key.'][route_option_id]', null, CHtml::listData($options, 'id', 'name'), array('empty' => '-- Select --'));
		} else {
			echo '-';
		}
	}

	public function getPrescriptionItems($element)
	{
		$items = $element->items;
		if (isset($_POST['prescription_item'])) {

			// Form has been posted, so we should return the submitted values instead
			$items = array();
			foreach ($_POST['prescription_item'] as $item) {
				$item_model = new OphDrPrescription_Item();
				$item_model->attributes = $item;
				if (isset($item['taper'])) {
					$tapers = array();
					foreach ($item['taper'] as $taper) {
						$taper_model = new OphDrPrescription_ItemTaper();
						$taper_model->attributes = $taper;
						$tapers[] = $taper_model;
					}
					$item_model->tapers = $tapers;
				}
				$items[] = $item_model;
			}

		} elseif (!$items) {

			// Prepopulate prescription with set by episode status
			// FIXME: It's brittle relying on the set name matching the status
			if ($episode = $this->patient->getEpisodeForCurrentSubspecialty()) {
				$items = array();
				$status_name = $episode->status->name;
				$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
				$subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
				$params = array(':subspecialty_id' => $subspecialty_id, ':status_name' => $status_name);
				$set = DrugSet::model()->find(array(
						'condition' => 'subspecialty_id = :subspecialty_id AND name = :status_name',
						'params' => $params,
				));
				if ($set) {
					foreach ($set->items as $item) {
						$item_model = new OphDrPrescription_Item();
						$item_model->drug_id = $item->drug_id;
						$item_model->loadDefaults();
						$items[] = $item_model;
					}
				}
			}

		}
		return $items;
	}

	public function actionDoPrint($id)
	{
		if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?',array($id))) {
			throw new Exception("Prescription not found for event id: $id");
		}

		$prescription->print = 1;
		$prescription->draft = 0;

		if (!$prescription->save()) {
			throw new Exception("Unable to save prescription: ".print_r($prescription->getErrors(),true));
		}

		if (!$event = Event::model()->findByPk($id)) {
			throw new Exception("Event not found: $id");
		}

		$event->info = 'Printed';

		if (!$event->save()) {
			throw new Exception("Unable to save event: ".print_r($event->getErrors(),true));
		}

		echo "1";
	}

	public function actionMarkPrinted()
	{
		if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?',array(@$_GET['event_id']))) {
			throw new Exception("Prescription not found for event id: ".@$_GET['event_id']);
		}

		if ($prescription->print == 1) {
			$prescription->print = 0;

			if (!$prescription->save()) {
				throw new Exception("Unable to save prescription: ".print_r($prescription->getErrors(),true));
			}
		}

		echo "1";
	}
}
