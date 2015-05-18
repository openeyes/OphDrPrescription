// we need to initialize the list of drug items
addSet($('#DrugSet_id').val());

// Disable currently prescribed drugs in dropdown
$('#prescription_items input[name$="[drug_id]"]').each(function (index) {
    var option = $('#common_drug_id option[value="' + $(this).val() + '"]');
    if (option) {
        option.data('used', true);
    }
});
applyFilter();

// Add selected common drug to prescription
$('body').delegate('#common_drug_id', 'change', function () {
    var selected = $(this).children('option:selected');
    if (selected.val().length) {
        addItem(selected.text(), selected.val());
        $(this).val('');
    }
    return false;
});

// Add selected drug set to prescription
$('body').delegate('#drug_set_id', 'change', function () {
    var selected = $(this).children('option:selected');
    if (selected.val().length) {
        addSet(selected.val());
        if (controllerName == 'DefaultController') {
            $(this).val('');
        }
        if (controllerName == 'AdminController') {
            $('#drugsetdata').show();
            $('.alert-box').hide();
        }
    }
    return false;
});

// Add repeat to prescription
$('body').delegate('#repeat_prescription', 'click', function () {
    addRepeat();
    return false;
});

// Clear prescription
$('body').delegate('#clear_prescription', 'click', function () {
    clear_prescription();
    applyFilter();
    return false;
});

// Update drug route options for selected route
$('body').delegate('select.drugRoute', 'change', function () {
    var selected = $(this).children('option:selected');
    if (selected.val().length) {
        var options_td = $(this).parent().next();
        var key = $(this).closest('tr').attr('data-key');
        $.get(baseUrl + "/OphDrPrescription/Default/RouteOptions", {
            key: key,
            route_id: selected.val()
        }, function (data) {
            options_td.html(data);
        });
    }
    return false;
});

// Remove item from prescription
$('#prescription_items').delegate('a.removeItem', 'click', function () {
    var row = $(this).closest('tr');
    var drug_id = row.find('input[name$="[drug_id]"]').first().val();
    var key = row.attr('data-key');
    $('#prescription_items tr[data-key="' + key + '"]').remove();
    decorateRows();
    var option = $('#common_drug_id option[value="' + drug_id + '"]');
    if (option) {
        option.data('used', false);
        applyFilter();
    }
    return false;
});

// Add taper to item
$('#prescription_items').delegate('a.taperItem:not(.processing)', 'click', function () {
    var row = $(this).closest('tr');
    var key = row.attr('data-key');
    var last_row = $('#prescription_items tr[data-key="' + key + '"]').last();
    var taper_key = (last_row.attr('data-taper')) ? parseInt(last_row.attr('data-taper')) + 1 : 0;

    // Clone item fields to create taper row
    var dose_input = row.find('td.prescriptionItemDose input').first().clone();
    dose_input.attr('name', dose_input.attr('name').replace(/\[dose\]/, "[taper][" + taper_key + "][dose]"));
    dose_input.attr('id', dose_input.attr('id').replace(/_dose$/, "_taper_" + taper_key + "_dose"));
    var frequency_input = row.find('td.prescriptionItemFrequencyId select').first().clone();
    frequency_input.attr('name', frequency_input.attr('name').replace(/\[frequency_id\]/, "[taper][" + taper_key + "][frequency_id]"));
    frequency_input.attr('id', frequency_input.attr('id').replace(/_frequency_id$/, "_taper_" + taper_key + "_frequency_id"));
    frequency_input.val(row.find('td.prescriptionItemFrequencyId select').val());
    var duration_input = row.find('td.prescriptionItemDurationId select').first().clone();
    duration_input.attr('name', duration_input.attr('name').replace(/\[duration_id\]/, "[taper][" + taper_key + "][duration_id]"));
    duration_input.attr('id', duration_input.attr('id').replace(/_duration_id$/, "_taper_" + taper_key + "_duration_id"));
    duration_input.val(row.find('td.prescriptionItemDurationId select').val());

    // Insert taper row
    var odd_even = (row.hasClass('odd')) ? 'odd' : 'even';
    var new_row = $('<tr data-key="' + key + '" data-taper="' + taper_key + '" class="prescription-tapier ' + odd_even + '"></tr>');
    new_row.append($('<td class="prescription-label"><span>then</span></td>'), $('<td></td>').append(dose_input), $('<td colspan="2"></td>'), $('<td></td>').append(frequency_input), $('<td></td>').append(duration_input), $('<td class="prescriptionItemActions"><a class="removeTaper"	href="#">Remove</a></td>'));
    last_row.after(new_row);

    return false;
});

// Remove taper from item
$('#prescription_items').delegate('a.removeTaper', 'click', function () {
    var row = $(this).closest('tr');
    row.remove();
    return false;
});

// Apply selected drug filter
$('body').delegate('.drugFilter', 'change', function () {
    applyFilter();
    return false;
});

// remove all the rows from the prescription table
function clear_prescription() {
    $('#prescription_items tbody tr').remove();
    $('#common_drug_id option').data('used', false);
}

// Add repeat to prescription
function addRepeat() {
    $.get(baseUrl + "/OphDrPrescription/Default/RepeatForm", {
        key: getNextKey(),
        patient_id: OE_patient_id
    }, function (data) {
        $('#prescription_items').append(data);
        decorateRows();
        markUsed();
        applyFilter();
    });
}

// Add set to prescription
function addSet(set_id) {
    // we need to call different functions for admin and public pages here
    if (controllerName == 'DefaultController') {
        $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/SetForm", {
            key: getNextKey(),
            patient_id: OE_patient_id,
            set_id: set_id
        }, function (data) {
            $('#prescription_items').append(data);
            decorateRows();
            markUsed();
            applyFilter();
        });
    } else {
        $.getJSON(baseUrl + "/OphDrPrescription/PrescriptionCommon/SetFormAdmin", {
            key: getNextKey(),
            set_id: set_id
        }, function (data) {
            $('#set_name').val(data.drugsetName);
            $('#subspecialty_id').val(data.drugsetSubspecialtyId);
            clear_prescription();
            $('#prescription_items').append(data.tableRows);
            decorateRows();
            markUsed();
            applyFilter();
        });
    }
}

// Add item to prescription
function addItem(label, item_id) {
    // we need to call different functions for admin and public pages here
    if (controllerName == 'DefaultController') {
        $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/ItemForm", {
            key: getNextKey(),
            patient_id: OE_patient_id,
            drug_id: item_id
        }, function (data) {
            $('#prescription_items').append(data);
            decorateRows();
        });
    } else {
        $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/ItemFormAdmin", {
            key: getNextKey(),
            drug_id: item_id
        }, function (data) {
            $('#prescription_items').append(data);
            decorateRows();
        });
    }

    var option = $('#common_drug_id option[value="' + item_id + '"]');
    if (option) {
        option.data('used', true);
        applyFilter();
    }
}

// Mark used common drugs
function markUsed() {
    $('#prescription_items input[name$="\[drug_id\]"]').each(function (index) {
        var option = $('#common_drug_id option[value="' + $(this).val() + '"]');
        if (option) {
            option.data('used', true);
        }
    });
}

// Filter drug choices
function applyFilter() {
    var filter_type_id = $('#drug_type_id').val();
    var filter_preservative_free = $('#preservative_free').is(':checked');
    $('#common_drug_id option').each(function () {
        var show = true;
        var drug_id = $(this).val();
        if (drug_id) {
            if (filter_type_id && common_drug_metadata[drug_id].type_id != filter_type_id) {
                show = false;
            }
            if (filter_preservative_free && common_drug_metadata[drug_id].preservative_free == '0') {
                show = false;
            }
            if (show) {
                $(this).removeAttr("disabled");
            } else {
                $(this).attr("disabled", "disabled");
            }
        }
    });
}

// Fix odd/even classes on all rows
function decorateRows() {
    $('#prescription_items .prescriptionItem').each(function (i) {
        if (i % 2) {
            $(this).removeClass('even').addClass('odd');
        } else {
            $(this).removeClass('odd').addClass('even');
        }
        var key = $(this).attr('data-key');
        $('#prescription_items .prescriptionTaper[data-key="' + key + '"]').each(function () {
            if (i % 2) {
                $(this).removeClass('even').addClass('odd');
            } else {
                $(this).removeClass('odd').addClass('even');
            }
        });
    });
}

// Get next key for adding rows
function getNextKey() {
    var last_item = $('#prescription_items .prescriptionItem').last();
    return (last_item.attr('data-key')) ? parseInt(last_item.attr('data-key')) + 1 : 0;
}

