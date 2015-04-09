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
class AdminController extends BaseAdminController
{

	/**
	 * Render the main admin screen - currently not used
	 */
	public function actionIndex()
	{

	}

	/**
	 * @description Common drugs administration page - it lists the common drugs based on site and subspecialty
	 * @return html (rendered page)
	 */
	public function actionCommonDrugs()
	{
		/*
		 * We try to set default values for the selects
		 */
		if (isset($_GET["site_id"])) {
			$activesite = $_GET["site_id"];
		} else {
			$activesite = Yii::app()->session['selected_site_id'];
		}

		if (isset($_GET["subspecialty_id"])) {
			$activesubspecialty = $_GET["subspecialty_id"];
		} else {
			$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
			if (isset($firm->serviceSubspecialtyAssignment->subspecialty_id)) {
				$activesubspecialty = $firm->serviceSubspecialtyAssignment->subspecialty_id;
			} else {
				$activesubspecialty = null;
			}
		}

		$this->render('druglist', array(
			"selectedsite" => $activesite,
			"selectedsubspecialty" => $activesubspecialty,
			"site_subspecialty_drugs" => Element_OphDrPrescription_Details::model()->commonDrugsBySiteAndSpec($activesite,
				$activesubspecialty)
		));
	}

	/**
	 * @description Deletes a drug from the site_subspecialty_drug table - AJAX call only
	 * @param $ssd_id
	 * @return html
	 */
	public function actionCommonDrugsDelete($ssd_id)
	{
		/*
		 * We make sure to not allow deleting directly with the URL, user must come from the commondrugs list page
		 */
		if (!Yii::app()->request->isAjaxRequest) {
			$this->render("errorpage", array("errormessage" => "notajaxcall"));
		} else {
			if ($site_subspec_drug = SiteSubspecialtyDrug::model()->findByPk($ssd_id)) {
				$site_subspec_drug->delete();
				echo "success";
			} else {
				$this->render("errorpage", array("errormessage" => "recordmissing"));
			}
		}

	}


	/**
	 * @description Adds new drug into the site_subspecialty_drug table - AJAX call only
	 * @param $drug_id
	 * @param $site_id
	 * @param $subspecialty_id
	 * @return string
	 */
	public function actionCommonDrugsAdd($drug_id, $site_id, $subspec_id)
	{
		if (!Yii::app()->request->isAjaxRequest) {
			$this->render("errorpage", array("errormessage" => "notajaxcall"));
		} else {
			if (!is_numeric($drug_id) || !is_numeric($site_id) || !is_numeric($subspec_id)) {
				echo "error";
			} else {
				$newSSD = new SiteSubspecialtyDrug();
				$newSSD->site_id = $site_id;
				$newSSD->subspecialty_id = $subspec_id;
				$newSSD->drug_id = $drug_id;
				if ($newSSD->save()) {
					echo "success";
				} else {
					echo "error";
				}
			}
		}
	}
}