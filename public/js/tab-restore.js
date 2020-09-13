$(function () {
    let patient_id = $('#patientId').val();
    let id, type;
    if (patient_id) {
        type = 'activeTab-patient';
        id = patient_id;
    } else {
        return false;
    }

    $('a[data-toggle="pill"]').on('show.bs.tab', function (e) {
        let tab = $(e.target).attr('href');
        let activeDocument = { id:id, tab:tab};
        let activeDocumentSerialize = JSON.stringify(activeDocument);
        try {
            localStorage.setItem(type, activeDocumentSerialize);
        } catch (e) {
            if (e === QUOTA_EXCEEDED_ERR) {
                localStorage.clear();
            }
        }
    });

    const activeTab = JSON.parse(localStorage.getItem(type));
    if (activeTab && activeTab.id === id) {
        let tab = activeTab.tab;
        $('#document-tab a[href="' + tab + '"]').tab('show');
    }
});