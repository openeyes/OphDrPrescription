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
		if(Yii::app()->request->isAjaxRequest) {
			$criteria = new CDbCriteria();
			if(isset($_GET['term']) && $term = $_GET['term']) {
				$criteria->addCondition('LOWER(name) LIKE :term');
				$params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
			}
			if(isset($_GET['type_id']) && $type_id = $_GET['type_id']) {
				$criteria->addCondition('type_id = :type_id');
				$params[':type_id'] = $type_id;
			}
			if(isset($_GET['preservative_free']) && $preservative_free = $_GET['preservative_free']) {
				$criteria->addCondition('preservative_free = 1');
			}
			$criteria->params = $params;
			$drugs = Drug::model()->findAll($criteria);
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
		$item->loadDefaults();
		$this->renderPartial('form_Element_OphDrPrescription_Details_Item', array('key' => $key, 'item' => $item));
	}
	
}
