var verifyStatus = function (xhr) {
    if (xhr.status === 401 || xhr.status === 403) {
        window.location.href = '/login?redirectUrl=' + window.location.pathname;
    }
}

$(document).ajaxError(function (e, xhr, set, error) {
    verifyStatus(xhr);
});