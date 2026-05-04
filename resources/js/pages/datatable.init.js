// Datatable
// Set global DataTables defaults so all listing pages use 50 rows by default
if (typeof $.fn.dataTable !== 'undefined') {
    $.extend($.fn.dataTable.defaults, {
        pageLength: 50,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
    });
}

if ($('.datatable').length > 0) {
    $('.datatable').DataTable({
        "bFilter": false,
    });
}