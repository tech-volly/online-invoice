$(document).ready(function () {
    
    $('.product_category').select2();
    $('.tax_list').select2();

    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    var is_track_stock = $('input[name="is_track_stock"]:checked').val();
    if(is_track_stock == 'on') {
        $('#stock_report').show();
    }

    $('#is_track_stock').change(function() {
        if(this.checked) {
            $('#stock_report').show();
        }else {
            $('#stock_report').hide();
        }
    });

    //Remove product image
    $(document).on('click', '.removeProductImage', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'products/delete-product-image/'+id;
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
                        $('#productImageDiv').remove();
                        swal("Deleted! Image has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Image. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    //Validate product form
    jQuery.validator.addMethod("numerical", function(value, element) {
        return this.optional(element) || /^[0-9.]*$/.test(value);
    }, "Please enter numbers only please");

    $('#addEditProduct').validate({
        rules: {
            product_name: {
                required: true
            },
            category_id: {
                required: true
            },
            product_price: {
                required: true,
                numerical: true,
            },
            product_tax: {
                required: true,
            },
            product_description: {
                required: true
            },
            product_image: {
                required: function () {
                    if ($('#product_image_old').val() == "") {
                        return true;
                    } else {
                        return false;
                    }
                },
                extension: "jpg|jpeg|png|heic"
            },
            is_status: {
                required: true
            }
        },
        messages: {
            product_name: {
                required: 'Please enter product name'
            },
            category_id: {
                required: 'Please select product category'
            },
            product_price: {
                required: 'Plese enter product price',
                number: 'Please enter digit only'
            },
            product_tax: {
                required: 'Please select tax type',
            },
            product_description: {
                required: 'Please enter product description'
            },
            product_image: {
                required: 'Please select product image',
                accept: "Please select image only"
            },
            is_status: {
                required: 'Please select status'
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "product_price") {
                error.appendTo("#errorToShow");
            }else {
                error.insertAfter(element);
            }
        }
    });

    //Preview product image
    $('#product_image').change(function () {
        var file = $("input[type=file]").get(0).files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                $("#previewImg").attr("src", reader.result);
                $('#previewImg').show();
            } 
            reader.readAsDataURL(file);
        }
    });
    
    $(document).on('keyup', '#product_price, #product_purchase_price', function (e) {
        if($('#product_price').val() > 0 && $('#product_purchase_price').val() > 0) {
            var product_margin = $('#product_price').val() - $('#product_purchase_price').val();
            $('#product_margin').val(product_margin.toFixed(2));
        }
    });

});