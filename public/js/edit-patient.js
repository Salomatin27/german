$(function () {
    buildCheckboxes();
    addSimpleWindow();
    dateTimePicker(false,$('.datepicker'));
    dateTimePicker(true,$('.datetimepicker'));
    $('.input-error').addClass('is-invalid');

    $('#birthday').on('propertychange change input dp.change', function () {
        calculateAge();
    });

    patientPhotoHandler();
    images();
    calculateAge();

});

function calculateAge()
{
    const birthday = $('#birthday').val();
    const today = moment();
    let label = $('#age-label');
    let value = $('#age-value');
    let age = moment(birthday,'DD-MM-YYYY');
    if (!age._isValid) {
        label.hide();
        return;
    }
    age = today.diff(age, 'years');
    if (age && age > 0) {
        value.text(age);
        label.show();
    } else {
        label.hide();
    }

}


// object rows
function addOperationRow()
{
    const patient_id = getPatientId();
    $.ajax({
        url: '/operation/' + patient_id,
        type: 'post',
        success: function (data) {
            if (data === 'error') {
                messageBox('can\'t create operation', false);
                return false;
            }
            data = $(data);
            getImages(data);
            dateTimePicker(true,data.find('.datetimepicker'));
            data.find('.selectpicker').selectpicker();
            let table = $('#_operation_tbody');
            table.append(data);
            let top = $(data).offset().top;
            $('body, html').animate({scrollTop: top}, 500);

        },
        error: function (jqXhr, m, c) {
            messageBox('can\'t create operation', false);
        }
    });
    // let object_row = generateKey();
    // let table = $('#_operation tbody');
    // let row = $('#empty-rows .operation-row').clone();
    // row.find('input[name="operation[][operation_id]"]').val(object_row);
    // row.attr('data-id', object_row);
    // let inputs = row.find('label,input,select');
    // correctionEmptyRow(inputs, object_row);
    // getImages(row);
    // table.append(row);
    // dateTimePicker(true,row.find('.datepicker'));
}

function removeOperationRow(obj)
{
    let row = $(obj).parents('.operation-row');
    row.remove();
}

function getPatientId()
{
    return $('#patientId').val();
}

function createImageButton(id, element) // operation_id
{
    //debugger;
    const fileSelect = $('.fileSelect', element)[0];
    const fileElem = $('.file', element)[0];

    fileSelect.addEventListener("click", function (e) {
        e.preventDefault();
        if (fileElem) {
            fileElem.click(function (ex) {
            });
        }
    }, false);
    fileElem.addEventListener("change", function (e) {
        uploadImage(id);
    }, false);
}

function images()
{
    // foreach all operations for patient
    $('.operation-row').each(function (index) {
        const element = $(this);
        getImages(element);
    });
}

function getImages(element) // .operation-row
{
    const url = '/patient-images/';
    const operation_id = element.data('id');
    if (!Number(operation_id)) {
        return false;
    }
    $.ajax({
        url: url + operation_id,
        type: 'get',
        success: function (data) {
            let panel = $('.operation-photo', element);
            panel.empty().append(data);
            createImageButton(operation_id, element);
        },
        error: function (jqXhr, m, c) {
            //debugger;
            console.log('server error', 'warning');
        }
    });

}

function uploadImage(id) // operation_id
{
    let url = '/patient-images/';
    let form = $('#image-form_'+id);
    let data = new FormData(form[0]);

    $.ajax({
        url: url + id,
        type: 'post',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if (data) {
                messageBox('file uploaded', true);
                $('#image-panel_'+id).append(data);
                let image = $(data).find('img')[0];
                let image_id = $(image).attr('id');
                let top = $('#'+image_id).offset().top;
                $('body, html').animate({scrollTop: top}, 500);
            } else {
                messageBox('file not uploaded', false);
            }
        },
        error: function (jqXhr, m, c) {
            messageBox('file not uploaded', false);
        }
    });
    return false;
}

function patientPhotoHandler()
{
    const form = $('#patient-photo-form');
    const fileSelect = $('.patient-photo')[0];
    const fileElem = $('.file', form)[0];

    fileSelect.addEventListener("click", function (e) {
        e.preventDefault();
        if (fileElem) {
            fileElem.click(function (ex) {
            });
        }
    }, false);
    fileElem.addEventListener("change", function (e) {
        form.submit();
    }, false);
}