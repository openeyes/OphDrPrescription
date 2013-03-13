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

/**
 * The followings are the available columns in table 'et_ophdrprescription_details':
 * @property string $id
 * @property integer $event_id
 * @property string $comments
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property Item[] $items
 */
class Element_OphDrPrescription_Details extends BaseEventTypeElement {

	/**
	 * Returns the static model of the specified AR class.
	 * @return Element_OphDrPrescription_Details the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'et_ophdrprescription_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
				array('event_id, comments, draft', 'safe'),
				//array('', 'required'),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('id, event_id, comments', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
				'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
				'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
				'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
				'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
				'items' => array(self::HAS_MANY, 'OphDrPrescription_Item', 'prescription_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);
		$criteria->compare('comments', $this->comments, true);

		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
		));
	}

	public function getLetterText() {
		$return = '';
		foreach($this->items as $item) {
			if ($return) {
				$return .= "\n";
			}
			$return .= $item->getDescription();

			if ($item->tapers) {
				foreach ($item->tapers as $taper) {
					$return .= "\n   ".$taper->getDescription();
				}
			}
		}
		return $return;
	}
	
	public function commonDrugs() {
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		$subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
		$site_id = Yii::app()->request->cookies['site_id']->value;
		$params = array(':subSpecialtyId' => $subspecialty_id, ':siteId' => $site_id);
		return Drug::model()->findAll(array(
				'condition' => 'ssd.subspecialty_id = :subSpecialtyId AND ssd.site_id = :siteId',
				'join' => 'JOIN site_subspecialty_drug ssd ON ssd.drug_id = t.id',
				'order' => 'name',
				'params' => $params,
		));
	}

	public function drugSets() {
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		$subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
		$params = array(':subspecialty_id' => $subspecialty_id);
		return DrugSet::model()->findAll(array(
				'condition' => 'subspecialty_id = :subspecialty_id',
				'order' => 'name',
				'params' => $params,
		));
	}

	public function drugTypes() {
		return DrugType::model()->findAll(array(
				'order' => 'name',
		));
	}

	public function isEditable() {
		return true;
	}

	/**
	 * Validate prescription items
	 * @todo This probably doesn't belong here, but there doesn't seem to be an easy way
	 * of doing it through the controller at the moment
	 */
	protected function beforeValidate() {
		if(isset($_POST['prescription_items_valid']) && $_POST['prescription_items_valid']) {
				
			// Empty prescription not allowed
			if(!isset($_POST['prescription_item']) || !$_POST['prescription_item']) {
				$this->addError('prescription_item', 'Prescription cannot be empty');
				return parent::beforeValidate();
			}
				
			// Check that fields on prescription items are set
			foreach($_POST['prescription_item'] as $key => $item) {
				$item_model = new OphDrPrescription_Item();
				$item_model->drug_id = $item['drug_id'];
				$item_model->dose = $item['dose'];
				$item_model->route_id = $item['route_id'];
				$item_model->frequency_id = $item['frequency_id'];
				$item_model->duration_id = $item['duration_id'];
				if(isset($item['route_option_id'])) {
					$item_model->route_option_id = $item['route_option_id'];
				}

				// id and prescription_id are not yet available, so exclude from validation
				$validate_attributes = array_keys($item_model->getAttributes(false));
				if(!$item_model->validate($validate_attributes)) {
					$this->addErrors($item_model->getErrors());
				}

				if(isset($item['taper'])) {
					
					// Check that the taper fields are valid
					foreach($item['taper'] as $taper) {
						$taper_model = new OphDrPrescription_ItemTaper();
						$taper_model->dose = $taper['dose'];
						$taper_model->frequency_id = $taper['frequency_id'];
						$taper_model->duration_id = $taper['duration_id'];

						// id and item_id are not yet available, so exclude from validation
						$validate_attributes = array_keys($taper_model->getAttributes(false));
						if(!$taper_model->validate($validate_attributes)) {
							$this->addErrors($taper_model->getErrors());
						}
					}
				}
			}

		}
		return parent::beforeValidate();
	}

	/**
	 * Save prescription items
	 * @todo This probably doesn't belong here, but there doesn't seem to be an easy way
	 * of doing it through the controller at the moment
	 */
	protected function afterSave() {
		if(isset($_POST['prescription_items_valid']) && $_POST['prescription_items_valid']) {
				
			// Get a list of ids so we can keep track of what's been removed
			$existing_item_ids = array();
			$existing_taper_ids = array();
			foreach($this->items as $item) {
				$existing_item_ids[$item->id] = $item->id;
				foreach($item->tapers as $taper) {
					$existing_taper_ids[$taper->id] = $taper->id;
				}
			}
				
			// Process (any) posted prescription items
			$new_items = (isset($_POST['prescription_item'])) ? $_POST['prescription_item'] : array();
			foreach($new_items as $item) {
				if(isset($item['id']) && isset($existing_item_ids[$item['id']])) {
						
					// Item is being updated
					$item_model = OphDrPrescription_Item::model()->findByPk($item['id']);
					unset($existing_item_ids[$item['id']]);
						
				} else {
						
					// Item is new
					$item_model = new OphDrPrescription_Item();
					$item_model->prescription_id = $this->id;
					$item_model->drug_id = $item['drug_id'];
						
				}

				// Save main item attributes
				$item_model->dose = $item['dose'];
				$item_model->route_id = $item['route_id'];
				if(isset($item['route_option_id'])) {
					$item_model->route_option_id = $item['route_option_id'];
				} else {
					$item_model->route_option_id = null;
				}
				$item_model->frequency_id = $item['frequency_id'];
				$item_model->duration_id = $item['duration_id'];
				$item_model->save();

				// Tapering
				$new_tapers = (isset($item['taper'])) ? $item['taper'] : array();
				foreach($new_tapers as $taper) {
					if(isset($taper['id']) && isset($existing_taper_ids[$taper['id']])) {

						// Taper is being updated
						$taper_model = OphDrPrescription_ItemTaper::model()->findByPk($taper['id']);
						unset($existing_taper_ids[$taper['id']]);

					} else {

						// Taper is new
						$taper_model = new OphDrPrescription_ItemTaper();
						$taper_model->item_id = $item_model->id;

					}
					$taper_model->dose = $taper['dose'];
					$taper_model->frequency_id = $taper['frequency_id'];
					$taper_model->duration_id = $taper['duration_id'];
					$taper_model->save();
				}
			}

			// Delete remaining (removed) ids
			OphDrPrescription_ItemTaper::model()->deleteByPk(array_values($existing_taper_ids));
			OphDrPrescription_Item::model()->deleteByPk(array_values($existing_item_ids));
				
		}

		return parent::afterSave();
	}

	public function getInfotext() {
		if (!$this->printed) {
			return 'Draft';
		} else {
			return 'Printed';
		}
	}
}
