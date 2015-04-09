/**
 * Created by veta on 08/04/15.
 */

$(document).ready(function () {
    $('#site_id').change(function () {
        this.form.submit();
    });
    $('#subspecialty_id').change(function () {
        this.form.submit();
    });
});

function DeleteCommonDrug(ssd_id) {
    if (ssd_id === undefined) {
        return false;
    } else {

        $.ajax({
                url: "/OphDrPrescription/admin/commondrugsdelete?ssd_id=" + ssd_id,
                error: function () {
                    console.log("ERROR, something went wrong!");
                },
                success: function () {
                    // we can dynamicaly rebuild the list here, but I think we don't need to develop more code for that :)
                    window.location.reload();
                }
            }
        );
    }
}

function addItem(drug_id) {
    $.ajax({
            url: "/OphDrPrescription/admin/commondrugsadd?drug_id=" + drug_id + "&site_id=" + $('#site_id').val() + "&subspec_id=" + $('#subspecialty_id').val(),
            error: function () {
                console.log("ERROR, something went wrong!");
            },
            success: function () {
                // we can dynamicaly rebuild the list here, but I think we don't need to develop more code for that :)
                window.location.reload();
            }
        }
    );

}