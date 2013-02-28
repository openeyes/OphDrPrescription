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
			window.location.href = baseUrl+'/patient/episodes/'+et_patient_id;
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
						window.location.href = baseUrl+'/patient/episodes/'+et_patient_id;
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
	var m = window.location.href.match(/\/view\/([0-9]+)/);
	printPDF(baseUrl+'/OphDrPrescription/default/print/' + m[1],{});
}
