$(document).ready(function () {
    if ($('#invoiceDataTable').length > 0) {
        var invoiceTable = $('#invoiceDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0, 10],
                'orderable': false,
            }],
            processing: true,
            serverSide: true,
            pageLength: 50,
            columnDefs: [{
                targets: [0, 11],
                orderable: false
            }],
            ajax: {
                url: base_url + "invoices/get-data",
                type: "GET",
                data: function (d) {
                    d.client = $('#client').val();
                    d.payment_status = $('#payment_status').val();
                    d.from_payment_date = $('#from_payment_date').val();
                    d.to_payment_date = $('#to_payment_date').val();
                }
            },
            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'invoice_number' },
                { data: 'client' },
                { data: 'project' },
                { data: 'invoice_date' },
                { data: 'invoice_due_date' },
                { data: 'invoice_grand_total' },
                { data: 'payment_status' },
                { data: 'invoice_payment_date' },
                { data: 'categories' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false }
            ]

        });
    }

    $('.client_list').select2();
    $('.payment_options').select2();

    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    $('#invoiceDataTable').on('click', '.deleteInvoiceBtn', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'invoices/delete/' + id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this invoice details!",
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
                        $('#invoicerow_' + id).remove();
                        swal("Deleted! Invoice has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Invoice. Please try again.", {
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
        var url = base_url + 'invoices/delete-selected-records';
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

    $('#change_status, #view_invoice_notes,#change_project').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.invoiceDatetimepicker').datetimepicker({
        format: 'DD-MM-YYYY',
        icons: {
            up: "fa fa-angle-up",
            down: "fa fa-angle-down",
            next: 'fa fa-angle-right',
            previous: 'fa fa-angle-left'
        }
    });

    $('select#change_payment_id').on('change', function () {
        var selected_status = $(this).children("option:selected").text();
        if (selected_status == 'Paid') {
            $('#paymentDate').show();
        } else {
            $('#paymentDate').hide();
        }
    });

    $('#changeStatusBtn').on('click', function () {

        var myCheckboxes = new Array();
        var status = $('#change_payment_id').val();
        var payment_date = $('#invoice_payment_date').val();
        var payment_text = $('#change_payment_id').children("option:selected").text();
        $("input.cb-element:checked").each(function () {
            myCheckboxes.push($(this).val());
        });
        if (myCheckboxes.length == 0) {
            swal("Alert!", "Please select atleast one record to change the payment status.", "warning");
            $("#change_status").modal('hide');
            return false;
        }
        if (!status) {
            swal("Alert!", "Please select payment status.", "warning");
            return false;
        }
        if (payment_text == 'Paid' && !payment_date) {
            swal("Alert!", "Please select payment date.", "warning");
            return false;
        }
        var url = base_url + 'invoices/change-payment_status';
        swal({
            title: "Are you sure?",
            text: "You want to change the payment status for the selected record(s)?",
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
                        ids: myCheckboxes,
                        status: status,
                        payment_date: payment_date
                    },
                }).done(function (data) {
                    console.log(data.success);
                    if (data.success == 1) {
                        swal("Payment status is changed for selected records!", {
                            icon: "success",
                        });
                        window.location.reload();
                    } else {
                        swal("Oops! Error in changing the status. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    //code for edit project 
    $('#changeProjectBtn').on('click', function () {
        var dataId = $("#pidval").val();

        var project_id = $('#change_project_id').val();
        // if(!project_id) {
        //     swal("Alert!", "Please select Project.", "warning");
        //     return false;
        // }
        var url = base_url + 'invoices/edit-project';
        swal({
            title: "Are you sure?",
            text: "You want to change the Project for the selected record(s)?",
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
                        ids: dataId,
                        project_id: project_id
                    },
                }).done(function (data) {
                    console.log(data.success);
                    if (data.success == 1) {
                        swal("Project is changed for selected records!", {
                            icon: "success",
                        });
                        window.location.reload();
                    } else {
                        swal("Oops! Error in Edit the Project. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });
    //end code here for edit project
    //CODE FOR PRESELECT PROJECT IN EDIT PROJECT POPUP

    $(document).on('click', '.edit_project_link', function (event) {
        var prId = $(this).attr('pr-id');
        var dataId = $(this).attr('data-id');
        $("#pidval").val(dataId);
        // $('option:selected', this).remove();
        $("#change_project_id option:selected").removeAttr("selected");
        $('#change_project_id').select2();
        if (prId > 0) {

            $('#change_project_id option[value=' + prId + ']').attr("selected", "selected");
            $('#change_project_id').select2();
            $("#select2-change_project_id-container").parent().parent().parent().css("width", "100%");
        } else {
            $('#change_project_id option[value=""]').attr("selected", "selected");
            $('#change_project_id').select2();
            $("#select2-change_project_id-container").parent().parent().parent().css("width", "100%");
        }
    });
    //END CODE HERE

    $('#invoiceDataTable').on('click', '.viewNote', function () {
        var id = $(this).attr('data-id');
        var url = base_url + 'invoices/view-invoice-note/' + id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if (data.success) {
                if (data.invoice_notes) {
                    $('#invoice_notes').html(data.invoice_notes);
                } else {
                    $('#invoice_notes').html('No notes are added for this invoice.');
                }
                $('#view_invoice_notes').modal('show');
            }
        });
    });

    //Invoice Filter starts
    $('.invoice_date_range').datetimepicker({
        format: 'DD-MM-YYYY',
        icons: {
            up: "fa fa-angle-up",
            down: "fa fa-angle-down",
            next: 'fa fa-angle-right',
            previous: 'fa fa-angle-left'
        }
    });

    $(document).on('click', '#exportInvoiceByStatus', function (e) {
        e.preventDefault();
        var payment_status = $('#paymentOption').val();
        var client = $('#client').val();
        var from_payment_date = $('#from_payment_date').val();
        var to_payment_date = $('#to_payment_date').val();
        if (payment_status == null && client == null && from_payment_date == '' && to_payment_date == '') {
            swal("Oops! Please select atleast one payment option to export excel.", {
                icon: "error",
            });
        } else {
            var url = $(this).attr('href') + '?payment_status=' + payment_status + '&client=' + client + '&from_payment_date=' + from_payment_date + '&to_payment_date=' + to_payment_date;
            window.location.href = url;
        }
    });

    $(document).on('click', '#searchInvoiceResult', function (e) {
        e.preventDefault();
        var payment_status = $('#paymentOption').val();
        var client = $('#client').val();
        var from_payment_date = $('#from_payment_date').val();
        var to_payment_date = $('#to_payment_date').val();
        if (payment_status == null && client == null && from_payment_date == '' && to_payment_date == '') {
            swal("Oops! Please select atleast one search option to filter record.", {
                icon: "error",
            });
        } else {
            var url = $(this).attr('href') + '?payment_status=' + payment_status + '&client=' + client + '&from_payment_date=' + from_payment_date + '&to_payment_date=' + to_payment_date;
            window.location.href = url;
        }
    });

    $(document).on('click', '#resetInvoiceResult', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        window.location.href = url;
    });
    // Invoice Filter ends

    // Add this block inside your $(document).ready(function () { ... })
    // alongside the existing deleteInvoiceBtn handler

    $('#invoiceDataTable').on('click', '.cancelInvoiceBtn', function (e) {
        // alert("Cancel Invoice button clicked!"); // Debugging alert
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'invoices/cancel/' + id;

        swal({
            title: "Cancel Invoice?",
            text: "This invoice will be marked as Cancelled and a cancellation email will be sent to the client.",
            icon: "warning",
            buttons: {
                cancel: "No, go back",
                confirm: {
                    text: "Yes, Cancel it!",
                    value: true,
                    className: "btn-danger"
                }
            },
            dangerMode: true,
        }).then(function (willCancel) {
            if (willCancel) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                }).done(function (data) {
                    if (data.success == 1) {
                        var msg = "Invoice has been cancelled!";
                        if (data.email_sent) {
                            msg += " Cancellation email sent to the client.";
                        } else {
                            msg += " (Email could not be sent.)";
                        }
                        swal(msg, { icon: "success" });
                        invoiceTable.ajax.reload();   // reload DataTable in place
                    } else {
                        swal("Oops! " + (data.message || "Error cancelling invoice. Please try again."), {
                            icon: "error",
                        });
                    }
                }).fail(function () {
                    swal("Oops! Server error. Please try again.", { icon: "error" });
                });
            }
        });
    });
});