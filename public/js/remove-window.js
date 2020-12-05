function addRemoveWindow()
{
    let html = '<div id="removeWindow" class="modal fade" role="dialog">\n' +
        '    <div class="modal-dialog">\n' +
        '\n' +
        '        <!-- Modal content-->\n' +
        '        <div class="modal-content">\n' +
        '            <div class="modal-header">\n' +
        '                <h5 class="modal-title">LÖSCHEN <span class="english-label">remove</span></h5>\n' +
        '                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>\n' +
        '            </div>\n' +
        '            <div class="modal-body">\n' +
        '                <p id="modal-text1">Some text in the modal.</p>\n' +
        '                <p><span id="modal-text2" class="english-label"></span></p>\n' +
        '            </div>\n' +
        '            <div class="modal-footer">\n' +
        '                <button type="button" class="btn btn-secondary adds-edit" data-dismiss="modal">Stornierung<br><span class="english-label">cancel</span> </button>\n' +
        '                <button type="button" class="btn btn-primary adds-edit" data-dismiss="modal" id="submit">Löschen<br><span class="english-label">delete</span></button>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '\n' +
        '    </div>\n' +
        '</div>\n';
    $('main.container').append(html);
}

$(function () {
    addRemoveWindow();
    let remove_window = $('#removeWindow');

    remove_window.on('show.bs.modal', function (event) {
        //let object=$(event.relatedTarget);
        let modal = $(this);
        let text1 = modal.data('text1');
        let text2 = modal.data('text2');
        let title = modal.data('title');
        let submit = modal.data('submit');
        let element = modal.data('element');
        let url = modal.data('url');
        if (!url) {
            return false;
        }
        if (title) {
            modal.find('.modal-title').text(title);
        }
        if (text1) {
            modal.find('#modal-text1').text(text1);
        }
        if (text2) {
            modal.find('#modal-text2').text(text2);
        }
        let btn_send = modal.find('#submit');
        if (submit) {
            btn_send.text(submit);
        }
        btn_send.on('click', function () {
            if (!element) {
                //window.location = url;
                $.ajax({
                    url: url,
                    type: 'delete',
                    success: function (data) {
                        if (data.error) {
                            messageBox(data.error, false);
                        } else if (data.url) {
                            window.location.href = data.url;
                        }
                    },
                    error: function () {
                        messageBox('server error', false);
                    }
                });
            } else {
                $.ajax({
                    url: url,
                    type: 'delete',
                    success: function (data) {
                        if (data.error) {
                            messageBox(data.error, false);
                        } else {
                            $(element).remove();
                            messageBox('entfernt', true);
                        }
                    },
                    error: function () {
                        messageBox('server error', false);
                    }

                });
            }
        });
    });
    remove_window.on('hidden.bs.modal', function () {
        let modal = $(this);
        let btn_send=modal.find('#submit');
        btn_send.off('click');
    });

});

function removeDocument(url,text1,text2,object = false)
{
    let window = $('#removeWindow');
    window.data('url', url);
    window.data('text1', text1);
    window.data('text2', text2);
    if (object) {
        let elements = $(object).parents('.delete-object');
        if (elements.length > 0) {
            window.data('element', elements[0]);
        } else {
            return false;
        }

    }
    window.modal();
    return false;
}
