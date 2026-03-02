$(document).ready(function () {
    
    $('.supplier_list').select2();
    $('.project_list').select2();
    $('.supplier_category_list').select2();
    $('.payment_method_list').select2();
    $('.tax_selection').select2();
    
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    getSupplierExpenseCategories();
   
    function getSupplierExpenseCategories() {
        var supplier_id = $('#supplier_id').val();
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
                    let html;
                    if(data.success == 1) {
                        var hidden_val = $('#suppilier_sel_cat').val();
                        for (let i = 0; i < data.expense_categories.length; i++) {
                            html += '';
                            html += `<option value="${data.expense_categories[i].id}" ${hidden_val == data.expense_categories[i].id ? 'selected' : '' }  >${data.expense_categories[i].name}</option>`;
                        }
                        $('#supplier_expense_category').html(html);
                    }
                },
                error: function (error) {
                    console.log(error) 
                }
            });
        }
    }

    $('#supplier_id').change(function() {
        getSupplierExpenseCategories();
    });

    // jQuery.validator.addMethod("alphanumeric", function(value, element) {
    //     return this.optional(element) || /^[a-zA-Z0-9]*$/.test(value);
    // }, "Please enter letters and numbers only please");

    jQuery.validator.addMethod("numerical", function(value, element) {
        return this.optional(element) || /^[0-9.]*$/.test(value);
    }, "Please enter numbers only please");

    $('#addEditExpense').validate({
        rules: {
            supplier_invoice_number: {
                required: true,
                //alphanumeric: true
            },
            payment_method_id: {
                required: true
            },
            supplier_id: {
                required: true
            },
            supplier_expense_category: {
                required: true
            },
            expense_tax: {
                required: true
            },
            expense_amount: {
                required: true,
                numerical: true,
            },
            expense_date: {
                required: true
            },
            expense_description: {
                required: true
            },
            expense_attached_receipt: {
                // required: function () {
                //     if ($('#expense_attached_receipt_old').val() == "") {
                //         return true;
                //     } else {
                //         return false;
                //     }
                // },
                extension: "docx|doc|odt|pdf|jpg|jpeg|png|xlsx|txt|heic"
            },
            is_status: {
                required: true
            }
        },
        messages: {
            supplier_invoice_number: {
                required: 'Please enter invoice number'
            },
            paymenth_method_id: {
                required: 'Please select payment method'
            },
            supplier_id: {
                required: 'Please select supplier'
            },
            supplier_expense_category: {
                required: 'Please select supplier category'
            },
            expense_tax: {
                required: 'Please select tax type'
            },
            expense_amount: {
                required: 'Please enter amount',
            },
            expense_date: {
                required: 'Please select date'
            },
            expense_description: {
                required: 'Please enter description'
            },
            expense_attached_receipt: {
                // required: "Please select receipt for expense",
                extension: "Please upload valid file formats"
            },
            is_status: {
                required: 'Please select status'
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "expense_amount") {
                error.appendTo("#errorToShow");
            }else {
                error.insertAfter(element);
            }
        }
    });

    $(document).on('click', '.removeAttachedImage', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'expenses/delete-attached-image/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this!",
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
                    type: 'GET'
                }).done(function (data) {
                    if (data.success == 1) {
                        $('#attachedImageDiv').remove();
                        swal("Deleted! Receipt has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Receipt. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('.image-link').magnificPopup({
        type:'image',
        closeOnContentClick: true,
		mainClass: 'mfp-img-mobile',
		image: {
			verticalFit: true
		}
    });
});