jQuery(function($) {
    // Added bu Hoang Duc 13-12-2018 for show hide Admission block
    showHideCustomBlock();
    // showHideAdmissionProfile();
    // End by Hoang Duc
});
// Added bu Hoang Duc 13-12-2018 for show hide Admission block
function showHideCustomBlock() {
    var status = $('input[name="sales_stage"]').val();
    if (status !== "Closed Won") {
        $('div[data-block="LBL_CUSTOM_INFORMATION"]').hide();
    }
}

function showHideAdmissionProfile() {
    var admissionStatus = $('input[name="potentials_admission_status"]').val();

    if (admissionStatus !== "won") {
        $('.registration_fee').attr("style", "visibility: hidden;");

        $(".tuition, .image_3x4, .high_school_diploma_copy, .high_school_profile_notarized_copy, .high_school_score_report, .school_changing_request, .birth_certificate_copy, .id_card_coppy, .family_register_copy, .certificate_of_achievement_copy, .guardian_family_register_copy, .guardian_id_card_coppy, .agreement_scholarship_credit_authenticated, .certificate_english_test").hide();
    }
}
// End by Hoang Duc