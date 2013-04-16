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

var prescription_print_url;

$(document).ready(function() {
	handleButton($('#et_save_print'),function() {
		$('#Element_OphDrPrescription_Details_draft').val(0);
	});

	handleButton($('#et_print'),function(e) {
		do_print_prescription();
		e.preventDefault();
	});

	handleButton($('#et_deleteevent'));

	handleButton($('#et_canceldelete'),function(e) {
		if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/delete/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
		}
		e.preventDefault();
	});

	handleButton($('#et_cancel'),function(e) {
		$('#dialog-confirm-cancel').dialog({
			resizable: false,
			//height: 140,
			modal: true,
			buttons: {
				"Yes, cancel": function() {
					$(this).dialog('close');

					disableButtons();

					if (m = window.location.href.match(/\/update\/[0-9]+/)) {
						window.location.href = window.location.href.replace('/update/','/view/');
					} else {
						window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
					}
				},
				"No, go back": function() {
					$(this).dialog('close');
				}
			}
		});
		e.preventDefault();
	});

});

function do_print_prescription() {
	printIFrameUrl(OE_print_url,null);
}
