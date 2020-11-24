$(function () {
    $('#manufacturer').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        // change manufacturer -> refresh implants and fixations
        refreshOptions('implant');
        refreshOptions('fixation');
    });
});
