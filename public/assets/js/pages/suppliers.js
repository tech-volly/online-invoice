$(document).ready(function () {
    
    $('.supplier_country').select2();
    
    if ($('#supplierDataTable').length > 0) {
        var supplierTable = $('#supplierDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,6],
                'orderable': false,
            }]
        });
        supplierTable.buttons().container().appendTo( '.datatableButtons' );
    }

    //View Supplier Notes
    $('#supplierDataTable').on('click', '.viewNote', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'suppliers/view-supplier-note/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                if(data.supplier_notes) {
                    $('#supplier_notes').html(data.supplier_notes);
                }else {
                    $('#supplier_notes').html('No notes are added for this supplier.');
                }
                $('#view_supplier_notes').modal('show');
            }
        });
    });

    $('#view_supplier_notes').modal({
        backdrop: 'static',
        keyboard: false
    });

    // Edit Client Details
    var secondary_address = $('input[name="add_shipping_address"]:checked').val();
    if(secondary_address == 'on') {
        $('#secondary_address').hide();
    }

    if($('#is_contact_div').val() == 1) {
        $('#add_contact_section').show();
    }
    // Ends

    $('#add_shipping_address').change(function() {
        if(this.checked) {
            $('#secondary_address').hide();
        }else {
            $('#secondary_address').show();
        }
    });


    $('#add_contact_btn').click(function() {
        $('#add-more-contact').show();
        $('#is_contact_div').val('0');
    });

    $('#add_more_contacts').click(function() {
        var newel = $('.add-more-contact:last').clone(true).find("input").val("").end().appendTo(".add_contact_result");
        newel.find('.remove_contact_form').show();
        $(newel).insertAfter(".add-more-contact:last");

    });

    $('.remove_contact_form').click(function(e) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this contact details!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(this).closest(".add-more-contact").remove();
                swal("Deleted! Contact has been deleted!", {
                    icon: "success",
                });   
            }
        });
    });

    $('#supplierDataTable').on('click', '.deleteSupplierBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'suppliers/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this supplier details!",
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
                        $('#supplierrow_' + id).remove();
                        swal("Deleted! Supplier has been deleted!", {
                            icon: "success",
                        });
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this supplier details.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting Supplier. Please try again.", {
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
        var url = base_url + 'suppliers/delete-selected-records';
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
                        swal("Oops! You can not delete this supplier details.", {
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

    $('#addEditSupplier').validate({
        rules: {
            supplier_business_name: {
                required: true
            },
            'supplier_expense_category[]': {
                required: true
            },
            supplier_first_name: {
                required: true
            },
            supplier_last_name: {
                required: true
            },
            supplier_mobile: {
                required: true,
                //number: true,
                pattern:/^[0-9\s()]*$/
            },
            supplier_email: {
                required: true,
                email: true
            },
            is_status: {
                required: true
            }
        },
        messages: {
            supplier_business_name: {
                required: 'Please enter business name'
            },
            'supplier_expense_category[]': {
                required: 'Please select the expense categories'
            },
            supplier_first_name: {
                required: 'Please enter first name'
            },
            supplier_last_name: {
                required: 'Please enter last name'
            },
            supplier_mobile: {
                required: 'Please enter mobile number',
                pattern: 'Please enter valid number'
            },
            supplier_email: {
                required: 'Please enter email',
                email: 'Please enter valid email'
            },
            is_status: {
                required: 'Please select status'
            }
        }
    });

    $('#importSupplierForm').validate({
        rules: {
            import_suppliers_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_suppliers_file: {
                required: 'Please upload file to import suppliers',
                extension: 'Please upload csv only'
            }
        }
    });
});