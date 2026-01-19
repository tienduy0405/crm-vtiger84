jQuery(function($) {
    // Added by Hoang Duc 21-11-2018 for Custom field related_cpsubjectlist
    $('input[name="related_product"]').on(
        Vtiger_Edit_Js.referenceSelectionEvent, function (e, object) {
            $('select[name="related_cpsubjectlist"]').val('');
            $('select[name="related_cpsubjectlist"]').val($('select[name="related_cpsubjectlist"] option:eq(0)').val()).trigger('change');
            loadSubjestsListBaseOnProduct();
        });
    $('input[name="related_product"]').closest('div').find('a').on('click', function () {
        jQuery('input[name="related_product"]').val('');
        $('select[name="related_cpsubjectlist"]').val('');
        $('select[name="related_cpsubjectlist"]').val($('select[name="related_cpsubjectlist"] option:eq(0)').val()).trigger('change');
        loadSubjestsListBaseOnProduct();
    });
    
    var url = new URL(window.location.href);
    var sourceModule = url.searchParams.get("sourceModule");
    var sourceRecord = url.searchParams.get("sourceRecord");
    if (sourceModule && sourceRecord && sourceModule === "Products") {
        loadSubjestsListBaseOnProduct();
    }

    // Handle Subject list detail
    $('select[name="related_cpsubjectlist"]').on('change', function () {
       var dataSubjects = $(this).find(":selected").data('subject');
        handleSubjectList(dataSubjects);
        updateTotalScore();
    });

     // Handle Subject total score
    $('.subject_score').live("keyup", function () {
        updateTotalScore();
    });
    // Handle Title
    if (jQuery('input[name="potentialname"]').val()) {
        jQuery('input[name="potentialname"]').attr("style", "width: 70%");
    }

    $('select[name="registered_year"], select[name="related_cptermofregistration"]').on("change", function () {
        updateTitle();
    });

    $('input[name="contact_id"], input[name="related_product"]').on(
        Vtiger_Edit_Js.referenceSelectionEvent, function (e, object) {
            updateTitle();
        });
    $('input[name="contact_id"], input[name="related_product"]').closest('div').find('a').on('click', function () {
        $('input[name="potentialname"]').val('');

        jQuery('input[name="potentialname"]').attr("style", "width: 200px");
    });

    // 05-12-2018 auto set year base on term
    $('select[name="related_cptermofregistration"]').on('change', function () {
        $('select[name="registered_year"]').val($(this).find(':selected').data('year')).trigger('change');

    });

    // 11-12-2018 Show hide custom block
    showHideCustomBlock();
    $('select[name="sales_stage"]').on("change", function () {
       showHideCustomBlock();
       // showHideAdmissionProfile();
    });

    // showHideAdmissionProfile();
    $('select[name="potentials_admission_status"]').on("change", function () {
        // showHideAdmissionProfile();
    });

    // 18-12-2018 Show hide subject list block
    showHideSubjectList();
    $('select[name="potentials_form"]').on("change", function () {
        showHideSubjectList();
    });

    // 19-12-2018 To validate test_result_for_assessment field
    validateTestResultForAssessmentField();
    $('select[name="potentials_admissions_result"]').on("change", function () {
        validateTestResultForAssessmentField();
    });
    // End by Hoang Duc

    var salesStage = $("select[name='sales_stage']").val();
    if(salesStage == 'Closed Won') {
        $('select[name="sales_stage"]').prop('disabled', 'disabled');
    }

    var potentialsAdmissionStatus = $("select[name='potentials_admission_status']").val();
    if(potentialsAdmissionStatus === 'won') {
        $('select[name="potentials_admission_status"]').prop('disabled', 'disabled');
    }
});
function handleSubjectList(dataSubjects) {
    var arrSubjests = [];

    $('#subjects_detail tbody tr').remove();

    if (dataSubjects) {
        arrSubjests = dataSubjects.split(' |##| ');
        appentInputSubject(arrSubjests);

    }
}
function appentInputSubject(arrSubjests) {
    var index;

    for(index in arrSubjests) {
        $('#subjects_detail tbody').append('<tr>' +
            '<td width="50%">' +
            '<input type="hidden" name="subject_name[]" class="inputElement read-only center" value="' + arrSubjests[index] + '" readonly>' +
            app.vtranslate(arrSubjests[index]) +
            '</td>' +
            '<td width="50%"><input type="text" name="subject_score[]" class="inputElement center subject_score" value="" required onkeyup="formatNumber(this, \'float\')"  pattern="[0-9]+([,\\.][0-9]+)?"></td>' +
            '</tr>');
    }
}

// Added by Hoang Duc 21-11-2018 for Custom field related_cpsubjectlist
function loadSubjestsListBaseOnProduct() {
    var product = jQuery('input[name="related_product"]').val();
    // Refesh option
    jQuery('select[name="related_cpsubjectlist"] option').remove();
    appendOption('', app.vtranslate('JS_SELECT_OPTION'), '');

    if (product !== '') {
        for(index in subjectsList) {
            if (subjectsList[index]['crmid'] === product) {
                appendOption(subjectsList[index]['cpsubjectlistid'], subjectsList[index]['name'], subjectsList[index]['cpsubjectlist_subject']);
            }
        }
    }
}
function appendOptionSelected(value, html, dataSubjests) {
    jQuery('select[name="related_cpsubjectlist"]').append('<option value="' + value + '" data-subject="' + dataSubjests + '" selected>' + app.vtranslate(html) + '</option>');
}
function appendOption(value, html, dataSubjests) {
    jQuery('select[name="related_cpsubjectlist"]').append('<option value="' + value + ' " data-subject="' + dataSubjests + '">' + app.vtranslate(html) + '</option>');
}
// 30-11-2018
function updateTotalScore() {
    var tbodyScore = jQuery("#subjects_detail > tbody");

    if (tbodyScore.children().length != 0) {
         var total = 0.0;

        jQuery(".subject_score").each(function(){
            var score = jQuery(this).val();
            total += parseFloat( score ? score : 0 );
        });

        jQuery('input[name="test_result_for_assessment"]').val(total);

    } else {

        jQuery('input[name="test_result_for_assessment"]').val(0);
    }
}

function updateTitle() {
    var contactName = jQuery('#contact_id_display').val();
    var productName = jQuery('#related_product_display').val();
    var year = jQuery('select[name="registered_year"]').val();
    var term = jQuery('select[name="related_cptermofregistration"] :selected').text();
    term = term === app.vtranslate('JS_SELECT_OPTION') ? "" : term;

    jQuery('input[name="potentialname"]').val(contactName + " - " + productName + " - " + term + " - " + year);
    jQuery('input[name="potentialname"]').attr("style", "width: 70%");
}
// 11-12-2017
function showHideCustomBlock() {
    var status = $('select[name="sales_stage"]').val();
    if (status === "Closed Won") {
        $('div[data-block="LBL_CUSTOM_INFORMATION"]').show();
    } else {
        // Set value to emty
        $('select[name="potentials_admission_status"]').val("").trigger("change");

        $('div[data-block="LBL_CUSTOM_INFORMATION"]').hide();
    }
}

function showHideAdmissionProfile() {
    var admissionStatus = $('select[name="potentials_admission_status"]').val();

    if (admissionStatus === "won") {
        $('.registration_fee').attr("style", "visibility: visible;");
        $(".tuition, .image_3x4, .high_school_diploma_copy, .high_school_profile_notarized_copy, .high_school_score_report, .school_changing_request, .birth_certificate_copy, .id_card_coppy, .family_register_copy, .certificate_of_achievement_copy, .guardian_family_register_copy, .guardian_id_card_coppy, .agreement_scholarship_credit_authenticated, .certificate_english_test").show();
    } else {
        $('.registration_fee').attr("style", "visibility: hidden;");
        $('input[name="registration_fee"], input[name="tuition"], input[name="image_3x4"], input[name="high_school_diploma_copy"], input[name="high_school_profile_notarized_copy"], input[name="high_school_score_report"], input[name="school_changing_request"], input[name="birth_certificate_copy"], input[name="id_card_coppy"], input[name="family_register_copy"], input[name="certificate_of_achievement_copy"], input[name="guardian_family_register_copy"], input[name="guardian_id_card_coppy"], input[name="agreement_scholarship_credit_authenticated"], input[name="certificate_english_test"]').prop('checked', false);

        $(".tuition, .image_3x4, .high_school_diploma_copy, .high_school_profile_notarized_copy, .high_school_score_report, .school_changing_request, .birth_certificate_copy, .id_card_coppy, .family_register_copy, .certificate_of_achievement_copy, .guardian_family_register_copy, .guardian_id_card_coppy, .agreement_scholarship_credit_authenticated, .certificate_english_test").hide();
    }
}
// Added by Hoang Duc 18-12-2018
function showHideSubjectList() {
    var potentialsForm = jQuery("select[name='potentials_form']").val();

    if (!(potentialsForm === "history_study" || potentialsForm === "review_score_high_school")) {
        jQuery(".related_cpsubjectlist, .subject_detail").attr("style", "visibility: hidden;");
        jQuery('input[name="test_result_for_assessment"]').val(0);
        jQuery('select[name="related_cpsubjectlist"]').val($('select[name="related_cpsubjectlist"] option:eq(0)').val()).trigger('change');
    } else {
        jQuery(".related_cpsubjectlist, .subject_detail").attr("style", "visibility: visible;");
    }
}
// End by Hoang Duc
// Added by Hoang Duc 19-12-2018
function validateTestResultForAssessmentField() {
    var testResultForAssessment = jQuery("select[name='potentials_admissions_result']").val();

    if (testResultForAssessment === "pass") {
        $(".test_result_for_assessment.alignMiddle").append('<span class="redColor">*</span>');
        // $('input[name="test_result_for_assessment"]').data("rule-required", "true");
        $('input[name="test_result_for_assessment"]').attr("data-rule-required", "true");
        $('input[name="test_result_for_assessment"]').attr("aria-required", "true");
        $('input[name="test_result_for_assessment"]').attr("aria-invalid", "false");
    } else {
        $(".test_result_for_assessment.alignMiddle span").remove();
        $('input[name="test_result_for_assessment"]').data("rule-required", "false");
        $('input[name="test_result_for_assessment"]').attr("aria-required", "false");
    }
}
// End by Hoang Duc