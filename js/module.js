$(document).ready(function() {
	$('#et_print').unbind('click').click(function() {
		var m = window.location.href.match(/\/view\/([0-9]+)/);
		printUrl('/OphDrPrescription/Default/print/' + m[1]);
		return false;
	});
});