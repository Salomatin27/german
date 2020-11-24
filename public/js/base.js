function addSimpleWindow()
{
    let html = '<div id="simpleWindow" class="modal fade" role="dialog">\n' +
        '    <div class="modal-dialog">\n' +
        '\n' +
        '        <!-- Modal content-->\n' +
        '        <div class="modal-content">\n' +
        '            <div class="modal-header">\n' +
        '                <h4 class="modal-title"></h4>\n' +
        '                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
        '            </div>\n' +
        '            <div class="modal-body">\n' +
        '            </div>\n' +
        '            <div class="modal-footer">\n' +
        '                <button type="button" class="btn btn-secondary adds-edit" data-dismiss="modal">Отмена</button>\n' +
        '                <button type="button" class="btn btn-primary adds-edit" data-dismiss="modal" id="modal-submit">Ок</button>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '\n' +
        '    </div>\n' +
        '</div>\n';
    $('main.container').append(html);
}

function dateTimePicker(is_time,obj)
{
    let format = 'DD-MM-YYYY';
    if (is_time) {
        format = 'DD-MM-YYYY HH:mm';
    }
    obj.datetimepicker({
        locale: 'de',
        format: format,
        icons: {
            time: "fas fa-clock",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down",
            previous: 'fa fa-arrow-left',
            next: 'fa fa-arrow-right',
            today: 'fa fa-crosshairs',
            clear: 'fa fa-trash',
            close: 'fa fa-remove'
        },
        // tooltips: {
        //     today: 'сегодня',
        //     clear: 'удалить',
        //     close: 'закрыть',
        //     selectMonth: 'выбрать месяц',
        //     prevMonth: 'предыдущий месяц',
        //     nextMonth: 'следующий месяц',
        //     selectYear: 'выбрать год',
        //     prevYear: 'предыдущий год',
        //     nextYear: 'следующий год',
        //     selectDecade: 'выбрать десятилетие',
        //     prevDecade: 'предыдущее десятилетие',
        //     nextDecade: 'следующее десятилетие',
        //     prevCentury: 'предыдущий век',
        //     nextCentury: 'следующий век'
        // }
    });
}

function buildCheckboxes()
{
    let checkboxes = $('input:checkbox');
    checkboxes.each(function () {
        changeCheckbox($(this));
    });
}

function changeCheckbox(obj)
{
    let element = $(obj);
    let id = element.attr('id');
    let checked_text = '<label '+'for="'+id+'" class="custom-control-label">'+element.data('checked')+'</label>';
    let unchecked_text = '<label '+'for="'+id+'" class="custom-control-label">'+element.data('unchecked')+'</label>';
    element.parent().find('label').remove();

    if (element.prop('checked')) {
        element.after(checked_text);
    } else {
        element.after(unchecked_text);
    }
}

function messageBox(message, is_success)
{
    let card;
    if (is_success) {
        card = $('<div class="alert alert-dismissible alert-success" style="position: fixed; top: 11rem;right: 3rem;z-index: 1200">\n' +
            '  <button type="button" class="close" data-dismiss="alert">&times;</button>\n' +
            '  <strong>Durchgeführt (Performed): </strong> '+message+'\n' +
            '</div>');
    } else {
        card = $('<div class="alert alert-dismissible alert-warning" style="position: fixed; top: 11rem;right: 3rem;z-index: 1200">\n' +
            '  <button type="button" class="close" data-dismiss="alert">&times;</button>\n' +
            '  <strong>Problem: </strong> '+message+'\n' +
            '</div>');
    }
    $('body').prepend(card);
    card.fadeOut(5000, function () {
        this.remove();
    });

}
