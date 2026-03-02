$(document).ready(function () {
    
    if($('#expectedExpensesDataTable').length > 0) {
        $('#expectedExpensesDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3],
                'orderable': false,
            }]
        });
    }
    
    $('#add_expected_expense').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#addExpectedExpenseForm').validate({
        rules: {
            expected_expense_year: {
                required: true
            },
            is_status: { 
                required: true, 
            }
        },
        messages: {
            expected_expense_year: {
                required: 'Please select year'
            },
            is_status: { 
                required: 'Please select status', 
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "expected_expense_year") {
                error.appendTo("#errorToShow");
            }else {
                error.insertAfter(element);
            }
        }
    });

    $('#expectedExpensesDataTable').on('click', '.deleteExpectedExpenseBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'expected-expenses/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this expected expense details!",
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
                        $('#expectedexpenserow_' + id).remove();
                        swal("Deleted! Expected expense has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Expected expense. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
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
        var url = base_url + 'expected-expenses/delete-selected-records';
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