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

    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        //e.target // newly activated tab
        //e.relatedTarget // previous active tab
        changePosition();
        $('a[data-toggle="pill"]').off('shown.bs.tab');
    })

    const activeTab = JSON.parse(localStorage.getItem(type));
    if (activeTab && activeTab.id === id) {
        let tab = activeTab.tab;
        $('#document-tab a[href="' + tab + '"]').tab('show');
    }

});

function changePosition()
{
    const address = location.href;
    const index = address.indexOf('#');
    if (index !== -1) {
        const caller_text = address.slice(index);
        const caller_object = $(caller_text);
        const position = caller_object.offset().top - 200;
        $('body, html').animate({scrollTop: position}, 500);
    }
}
