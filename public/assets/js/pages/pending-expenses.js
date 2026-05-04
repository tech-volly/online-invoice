$(document).ready(function () {  
    $('#pendingExpenseDataTable').DataTable({
        "aaSorting": [],
        'columnDefs': [{
            'targets': [0,6],
            'orderable': false,
        }]
    });

    $('#import_pending_expense').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#importPendingExpenseForm').validate({
        rules: {
            import_pending_expense_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_pending_expense_file: {
                required: 'Please upload file to import pending expense',
                extension: 'Please upload csv only'
            }
        }
    });

    $('#pendingExpenseDataTable').on('change', '.supplier_list', function(e) {
        e.preventDefault();
        let parentRow = $(this).parents("tr");
        let supplier_id = $('.supplier_list', parentRow).val();
        if(supplier_id) {
            var url = base_url + 'expenses/get-supplier-categories/'+supplier_id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                dataType: 'json',
                type: "GET",
                success: function (data) {
                    console.log(data)
                    let html;
                    if(data.success == 1) {
                        for (let i = 0; i < data.expense_categories.length; i++) {
                            html += '';
                            html += `<option value="${data.expense_categories[i].id}">${data.expense_categories[i].name}</option>`;
                        }
                        $('#supplier_expense_category', parentRow).html(html);
                    }
                },
                error: function (error) {
                    console.log(error) 
                }
            });
        }

    });

    $('#pendingExpenseDataTable').on('click', '.confirmExpenseBtn', function(e) {
        e.preventDefault();
        var url = base_url + 'pending-expense/confirm-pending-expense';

        let parentRow = $(this).parents("tr");
        let pending_expense_id = $(this).attr('data-id');

        var description = $('.expense_description', parentRow).attr('data-description');
        var amount = $('.expense_amount', parentRow).attr('data-amount');
        var expense_date = $('.expense_date', parentRow).attr('data-date');
        var supplier_id = $('.supplier_list', parentRow).val();
        var exp_category = $('.exp_category_list', parentRow).val();
        
        swal({
            title: "Are you sure?",
            text: "You want to confirm this expense details!",
            icon: "warning",
            buttons: true,
            dangerMode: false,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        description: description,
                        amount: amount,
                        expense_date: expense_date,
                        supplier_id: supplier_id,
                        exp_category: exp_category,
                        pending_expense_id: pending_expense_id
                    },
                }).done(function (data) {
                    if (data.success == 1) {
                        $('#pendingexpenserow_' + pending_expense_id).remove();
                        swal("Expense is confirmed!", {
                            icon: "success",
                        });
                        window.location.reload();
                    } else {
                        swal("Oops! Error in confirming the expense. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });
    
    var tableRows = $('#pendingExpenseDataTable').find('tbody > tr');
    tableRows.each(function (index) {
        var parentRow = $(this);
        $('.supplier_list', parentRow).select2();
    });

});