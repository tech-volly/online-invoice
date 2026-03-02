$(document).ready(function () {
    $('.expected_expense_year').select2();

    $("#add_new_row").click(function () { 
        var rowCount = $("#expectedExpenseListTable tbody tr").length;
        $("#expectedExpenseListTable").each(function () {
            var html = $("#hiddenExpectedExpenseListTable tbody tr").html();
            var tds = '<tr>';
            tds += html;
            tds += '</tr>';
            if ($('tbody', this).length > 0) {
                $('tbody', this).append(tds);
            } else {
                $(this).append(tds);
            }
        });
    });

    $("#expectedExpenseListTable").on( "click", '.removeRow' ,function(e) {
        $(this).closest('tr').remove();
    });


});