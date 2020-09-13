// pagination
let requestInProgress = false;
let currentPage = 0;
let isRows = true;

function checkBottom()
{
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
        $('#more').click();
    }
}

$(function () {

    // clear input button
    $('.has-clear input[type="text"]').on('input propertychange', function () {
        let $this = $(this);
        let visible = Boolean($this.val());
        $this.siblings('.form-control-clear').toggleClass('invisible', !visible);
    }).trigger('propertychange');

    $('.form-control-clear').click(function () {
        $(this).siblings('input[type="text"]').val('')
            .trigger('propertychange').focus();
    });

    // pagination
    $(window).scroll(function () {
        if (isRows) {
            checkBottom();
        }
    });
    $('#more').click(function () {
        if (requestInProgress) {
            return;
        }
        requestInProgress = true;
        search(currentPage,false);
    }).click();

});

// search
function search(page,is_start)
{
    let panel = $('#search-patients');
    let data = panel.serializeArray();
    if (is_start) {
        $('tr[data-id]').remove();
    }
    $('#img-load').show();
    $.ajax({
        url: window.location.pathname+'/'+page,
        type: 'GET',
        data: data,
        success: function (data) {
            if (is_start) {
                currentPage = 1;
            } else {
                currentPage++;
            }
            if (data.length < 1) {
                isRows = false;
                return;
            }
            $('table>tbody').append(data);
            requestInProgress = false;
            checkBottom();
        },
        complete: function () {
            $('#img-load').hide();
        }
    });
}