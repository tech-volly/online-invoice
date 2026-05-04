$(document).ready(function () {
    if ($('#expenseDataTable').length > 0) {
        var expenseTable = $('#expenseDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0, 8, 9],
                'orderable': false,
            }],
            processing: true,
            serverSide: true,
            pageLength: 50, // default limit
            ajax: {
                url: base_url + 'expenses/get-data',
                type: 'GET'
            },
            columns: [
                { data: 'id' },
                { data: 'business_name' },
                { data: 'invoice' },
                { data: 'project_name' },
                { data: 'expense_date' },
                { data: 'amount' },
                { data: 'gst' },
                { data: 'payment_method' },
                { data: 'category' },
                { data: 'receipt', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    }

    $('#import_expense').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#expenseDataTable').on('click', '.deleteExpenseBtn', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'expenses/delete/' + id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this expense details!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'DELETE'
                }).done(function (data) {
                    if (data.success == 1) {
                        $('#expenserow_' + id).remove();
                        swal("Deleted! Expense has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Expense. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('.image-link').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }
    });

    $('.doc-link').magnificPopup({
        type: 'iframe',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }
    });

    //Delete selected records
    $("#checkall").change(function () {
        if ($("#checkall").is(':checked')) {
            $(".cb-element").each(function () {
                $(this).prop("checked", true);
            });
        } else {
            $(".cb-element").each(function () {
                $(this).prop("checked", false);
            })
        }
    });

    $('#btnAllDelete').on('click', function () {
        var myCheckboxes = new Array();
        $("input.cb-element:checked").each(function () {
            myCheckboxes.push($(this).val());
        });
        if (myCheckboxes.length == 0) {
            swal("Alert!", "Please select atleast one record to delete.", "warning");
            return false;
        }
        var url = base_url + 'expenses/delete-selected-records';
        swal({
            title: "Are you sure?",
            text: "You want to delete the selected record(s)?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
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
                        ids: myCheckboxes
                    },
                }).done(function (data) {
                    console.log(data);
                    console.log(data.success);
                    if (data.success == 1) {
                        swal("Selected records  has been deleted!", {
                            icon: "success",
                        });
                        window.location.reload();
                    } else {
                        swal("Oops! Error in deleting selected records. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });
});