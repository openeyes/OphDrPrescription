$(document).ready(function() {
	$('#et_save_print').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			$('#Element_OphDrPrescription_Details_draft').val(0);
			return true;
		}
		return false;
	});

	$('#et_print').unbind('click').click(function() {
		do_print_prescription();
		return false;
	});

	$('#et_deleteevent').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			$('#et_deleteevent').click();
		}
		return false;
	});

	$('#et_canceldelete').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();

			if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
				window.location.href = window.location.href.replace('/delete/','/view/');
			} else {
				window.location.href = '/patient/episodes/'+et_patient_id;
			}
		}
		return false;
	});

	$('#et_cancel').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
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
							window.location.href = '/patient/episodes/'+et_patient_id;
						}
					},
					"No, go back": function() {
						$(this).dialog('close');
						return false;
					}
				}
			});
		}
		return false;
	});

});

function do_print_prescription() {
	var m = window.location.href.match(/\/view\/([0-9]+)/);
	printUrl('/OphDrPrescription/Default/print/' + m[1], null, module_css_path);
}

