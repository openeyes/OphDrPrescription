<?php

/**
 * Created by PhpStorm.
 * User: veta
 * Date: 20/04/15
 * Time: 16:40
 */
class CommonDrugAdminController extends BaseAdminController
{

	public function actionList()
	{
		$admin = new AdminListAutocomplete(SiteSubspecialtyDrug::model(), $this);

		$admin->setListFields(array(
			'id',
			'drugs.name',
			'drugs.dose_unit'
		));

		$admin->setCustomDeleteURL('/OphDrPrescription/admin/commondrugsdelete');
		$admin->setCustomSaveURL('/OphDrPrescription/admin/commondrugsadd');
		$admin->setFilterFields(
			array(
				array(
					'label' => 'Site',
					'dropDownName' => 'site_id',
					'defaultValue' => Yii::app()->session['selected_site_id'],
					'listModel' => Site::model(),
					'listIdField' => 'id',
					'listDisplayField' => 'short_name'
					),
				array(
					'label' => 'Subspecialty',
					'dropDownName' => 'subspecialty_id',
					'defaultValue' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id,
					'listModel' => Subspecialty::model(),
					'listIdField' => 'id',
					'listDisplayField' => 'name'
				)
			)
		);
		// we set default search options
		if ($this->request->getParam('search') == '') {
			$admin->getSearch()->initSearch(array(
					'filterid' =>
						array(
							'site_id' => Yii::app()->session['selected_site_id']
						),
					'subspecialty_id' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id
				)
			);
		}

		$admin->setAutocompleteField(
			array(
				'fieldName' => 'drug_id',
				'jsonURL' => '/OphDrPrescription/default/DrugList',
				'placeholder' => 'search for drugs'
			)
		);
		//$admin->searchAll();
		$admin->listModel();
	}

}