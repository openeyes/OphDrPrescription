<?php

class DefaultController extends BaseEventTypeController {
	public function actionCreate() {
		parent::actionCreate();
	}

	public function actionUpdate($id) {
		parent::actionUpdate($id);
	}

	public function actionView($id) {
		parent::actionView($id);
	}

	public function updateElements($elements, $data, $event) {
		// TODO: Move model aftersave stuff in here
		return parent::updateElements($elements, $data, $event);
	}
	
	public function actionDrugList() {
		if(Yii::app()->request->isAjaxRequest && isset($_GET['term'])) {
			$drugs = Drug::model()->findAll('LOWER(name) LIKE :term', array(
				':term' => '%' . strtolower(strtr($_GET['term'], array('%' => '\%'))) . '%',
			));
			$return = array();
			foreach($drugs as $drug) {
				$return[] = array(
						'label' => $drug->name,
						'value' => $drug->name,
						'id' => $drug->id,
				);
			}
			echo CJSON::encode($return);
		}
	}
	
	public function actionItemForm($key, $drug_id) {
		$item = new OphDrPrescription_Item();
		$item->drug_id = $drug_id;
		$this->renderPartial('form_Element_OphDrPrescription_Details_Item', array('key' => $key, 'item' => $item));
	}
	
}
