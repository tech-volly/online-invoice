$(document).ready(function () {
    if($('#paymentStatusDataTable').length > 0) {
        $('#paymentStatusDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3],
                'orderable': false,
            }],
        });
    }
    
    $('#edit_payment_status, #add_payment_status').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#paymentStatusDataTable').on('click', '.deletePaymentStatusBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/payment-statuses/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this payment status details!",
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
                        $('#paymentstatusrow' + id).remove();
                        swal("Deleted! Payment Status has been deleted!", {
                            icon: "success",
                        });
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this Payment Status as it is used in Invoices.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting Payment Status. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('#paymentStatusDataTable').on('click', '.editPaymentStatus', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'master/payment-statuses/edit/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                $('#payment_status_id').val(data.payment_status.id);
                $('#update_payment_status_name').val(data.payment_status.name);
                let html;
                html += '';
                html += '<option>Select Status</option>';
                html += `<option value="1" ${data.payment_status.is_status === 1 ? 'selected' : '' }>Active</option>`;
                html += `<option value="0" ${data.payment_status.is_status === 0 ? 'selected' : '' }>Inactive</option>`;
                $('#update_is_status').html(html);
                $('#edit_payment_status').modal('show');
            }
        });
    });

    $('#addPaymentStatus').validate({
        rules: {
            name: {
                required: true
            },
            is_status: {
                required: true
            }
        },
        messages: {
            name: {
                required: 'Please enter payment status name'
            },
            is_status: {
                required: 'Please select status'
            }
        } 
    });

    $('#editPaymentStatusForm').validate({
        rules: {
            name: {
                required: true
            },
            is_status: {
                required: true
            }
        },
        messages: {
            name: {
                required: 'Please enter payment status name'
            },
            is_status: {
                required: 'Please select status'
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
        var url = base_url + 'master/payment-statuses/delete-selected-records';
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
                        swal("Oops! You can not delete this payment status as it is used in Invoice.", {
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