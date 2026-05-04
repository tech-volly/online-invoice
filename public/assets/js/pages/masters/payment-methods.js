$(document).ready(function () {
    if($('#paymentMethodDataTable').length > 0) {
        var paymentMethodTable = $('#paymentMethodDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3],
                'orderable': false,
            }],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="las la-file-export"></i> Export Payment Methods',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [1,2] 
                    }
                }
            ]
        });
        paymentMethodTable.buttons().container().appendTo( '.datatableButtons' );
    }
    
    $('#edit_payment_method, #add_payment_method, #import_payment_methods').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#paymentMethodDataTable').on('click', '.deletePaymentMethodBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/payment-methods/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this payment method details!",
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
                        $('#paymentmethodrow_' + id).remove();
                        swal("Deleted! Payment Method has been deleted!", {
                            icon: "success",
                        });
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this payment method as it is used in Expenses.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting Payment Method. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('#paymentMethodDataTable').on('click', '.editPaymentMethod', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'master/payment-methods/edit/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                $('#payment_method_id').val(data.payment_method.id);
                $('#update_payment_method_name').val(data.payment_method.payment_method_name);
                let html;
                html += '';
                html += '<option>Select Status</option>';
                html += `<option value="1" ${data.payment_method.is_status === 1 ? 'selected' : '' }>Active</option>`;
                html += `<option value="0" ${data.payment_method.is_status === 0 ? 'selected' : '' }>Inactive</option>`;
                $('#update_is_status').html(html);
                $('#edit_payment_method').modal('show');
            }
        });
    });

    $('#addPaymentMethod').validate({
        rules: {
            payment_method_name: {
                required: true
            },
            is_status: {
                required: true
            }
        },
        messages: {
            payment_method_name: {
                required: 'Please enter payment method name'
            },
            is_status: {
                required: 'Please select status'
            }
        } 
    });

    $('#editPaymentMethodForm').validate({
        rules: {
            payment_method_name: {
                required: true
            },
            is_status: {
                required: true
            }
        },
        messages: {
            payment_method_name: {
                required: 'Please enter payment method name'
            },
            is_status: {
                required: 'Please select status'
            }
        } 
    });

    $('#importPaymentMethodForm').validate({
        rules: {
            import_payment_method_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_payment_method_file: {
                required: 'Please upload file to import payment methods',
                extension: 'Please upload csv only'
            }
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
        var url = base_url + 'master/payment-methods/delete-selected-records';
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
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this payment method as it is used in Expenses.", {
                            icon: "error",
                        });
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