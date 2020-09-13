var caller = null;

function reference(item, caller_object = null)
{
    $.ajax({
        url: '/reference/'+item,
        type: 'GET',
        success: function (data) {
            if (caller_object) {
                caller = $(caller_object);
            }
            $('#patient').hide();
            let ref=$('#reference');
            ref.empty();
            ref.append(data);
            ref.show();
        }
    });

}
function editReference(event)
{
    const object=$(event.currentTarget);
    const id=object.data('id');
    const name=object.data('name');
    const address=object.data('address');
    const ref=object.data('ref');
    let panel=$('#data-panel');

    if (id===0) {
        // new row
        $('.list-group-item[data-id="0"]',panel).remove();
        $('.list-group-item-heading',panel)
            .after('<div class="list-group-item" data-id="0"></div>');
    }
    let exist_form=$('form[name="reference-form"]');
    let exist_id=exist_form.parent().attr('data-id');
    exist_form.remove();
    $('.list-group-item[data-id="'+exist_id+'"]', panel).find('.row').show();

    let row=$('.list-group-item[data-id="'+id+'"]',panel);
    row.find('.row').hide();
    let fields='';
    if (ref==='clinic') {
        fields='<div class="col-sm-6"><input type="text" placeholder="name" name="name" class="form-control" value="'+name+'"></div>'
            + '<div class="col-sm-6"><input type="text" placeholder="address" name="address" class="form-control" value="'+address+'"></div>';
    } else {
        fields='<div class="col-sm-6"><input type="text" placeholder="name" name="name" class="form-control" value="'+name+'"></div>';
    }
    let form = '<form name="reference-form" method="post" autocomplete="off" id="reference-form" >'
        + '<input type="hidden" name="id" value="'+id+'">'
        + '<div class="row">'
        + fields
        + '</div><br><div class="row">'
        + '<div class="col-sm-6 col-md-4">'
        + '<button type="submit" onclick="updateReference(\''+ref+'\')" class="btn btn-success"><i class="far fa-save" aria-hidden="true"></i> СОХРАНИТЬ</button> '
        + '</div>'
        + '<div class="col-sm-6 col-md-4">'
        + '<a class="text-secondary" href="javascript:void(0)" onclick="cancelReference('+id+')"><i class="fas fa-times" aria-hidden="true"></i> ЗАКРЫТЬ</a>'
        + '</div></div></form>';
    row.append(form);
}
function cancelReference(id)
{
    let panel=$('#data-panel');
    if (id===0) {
        $('.list-group-item[data-id="0"]', panel).remove();
    } else {
        var exist_form=$('form[name="reference-form"]',panel);
        exist_form.remove();
        $('.list-group-item[data-id="'+id+'"]', panel).find('.row').show();
    }
}
function find(val)
{
    //var value_find=val.value.toLowerCase();
    var value_find=new RegExp(val.value,'ig');
    //var length_find=value_find.length;
    var length_find=val.value.length;
    var panel=$('#data-panel');
    if (length_find<3) {
        $('.list-group-item', panel).show();
        return false;
    }
    var dates=$('.value-ref');
    dates.each(function (index) {
        var name=$(this).attr('data-name');
        var id=$(this).attr('data-id');
        //var s_name=name.substr(0,length_find).toLowerCase();
        //var s_name=name.substr(0,length_find);
        if (!name.match(value_find)) {
            $('.list-group-item[data-id="'+id+'"]', panel).hide();
        } else {
            $('.list-group-item[data-id="'+id+'"]', panel).show();
        }

    });
    return false;
}
function updateReference(ref)
{
    const panel=$('#data-panel');
    //let keyword =  ref;
    let element = $('form[name="reference-form"]');
    let params = $(element).serializeArray();
    //params.push({name: 'keyword', value: keyword});
    let id = params[0]['value'];
    // var name = params[1]['value'];
    // var code = params[2]['value'];
    // var isActive = params[3]['value'];
    $(element).remove();
    let row = $('.list-group-item[data-id="'+id+'"]',panel);
    $.ajax({
        url: '/reference/' + ref,
        type: 'post',
        data: params,
        success: function (data) {
            row.replaceWith(data);
        }
    });

    return false;
}

function closeReference(keyword)
{
    let ref=$('#reference');
    ref.hide();
    ref.empty();
    let opt=$('select[data-db="'+keyword+'"]');
    const url='/reference-options/'+keyword;

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            opt.each(function () {
                let selectpicker = $(this);
                const current=selectpicker.val();
                selectpicker.empty();
                selectpicker.append(data);
                selectpicker.selectpicker('refresh');
                selectpicker.selectpicker('val',current);
            });
        }
    });
    $('#patient').show('fast', function () {
        // if (navigator.userAgent.match(/Chrome|AppleWebKit/)) {
        //     window.location.href = "#edit-item-well";
        //     window.location.href = "#edit-item-well";
        // } else {
        //     window.location.hash = "edit-item-well";
        // }
        if (caller) {
            let top = caller.offset().top - 200;
            $('body, html').animate({scrollTop: top}, 500);
        }

    });
}
