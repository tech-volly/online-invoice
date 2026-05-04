$(document).ready(function () {
    
    $('.client_list').select2();
    $('.brand_list').select2();
    $('.payment_status_list').select2();
    
    //Inclusive Type Tax
    let final_total = 0;
    let inclusive_tax = 0;
    let inclusive_tax_amt;
    let item_total = 0;
    //Exclusive Type Tax
    let ex_product_total = 0;
    let exclusiv_tax_amt = 0;
    let round_exclusive_tax = 0;
    //No GST
    let ng_item_total = 0;

    //Edit Invoice Values:
    var editTable = $('#invoiceProductTable');
    var editRows = editTable.find('tbody > tr');
    editRows.each(function (index) {
        var parentRow = $(this);
        toDisplayAllCalculations(parentRow);
    });
    
    var current_url = window.location.href;
    var split_url = current_url.split('/');

    $("#add_new_row").click(function () { 
        var rowCount = $("#invoiceProductTable tbody tr").length;
        $("#invoiceProductTable").each(function () {
            if(rowCount > 0) {
                var tds = '<tr class="productClass">';
                jQuery.each($('tr:last td', this), function (index, element) {
                    tds += '<td>' + $(this).html() + '</td>';
                });
                tds += '</tr>';
            }
            if(split_url[5] == 'edit') {
                var html = $("#hiddenTable tbody tr").html();
                var tds = '<tr class="productClass">';
                tds += html;
                tds += '</tr>';
            }
            
            if ($('tbody', this).length > 0) {
                $('tbody', this).append(tds);
            } else {
                $(this).append(tds);
            }
        });
    });

    $("#invoiceProductTable").on( "change", '.product_id' ,function(e) {
        let parentRow = $(this).parents("tr");
        let product_id = $('.product_id', parentRow).val();
        $('#hidden_prod_id',parentRow).val(product_id);
        var check = 1;
        getProductDetails(parentRow, product_id, check)
        // setTimeout(() => {
        //     toDisplayAllCalculations(parentRow);
        // }, 100);
    });

    $('#invoiceProductTable').on( "keyup mouseup", '.product_quantity', function(e) {
        let parentRow = $(this).parents("tr");
        toDisplayAllCalculations(parentRow);
    });

    $('#invoiceProductTable').on( "keyup", '.product_unit_price', function(e) {
        let parentRow = $(this).parents("tr");
        toDisplayAllCalculations(parentRow);
    });

    $("#invoiceProductTable").on( "click", '.removeRow' ,function(e) {
        $(this).closest('tr').remove();
        let parentRow = $(this).parents("tr");
        toDisplayAllCalculations(parentRow);
    });
    
    $('#invoiceProductTable').on( "change", '.tax_selection', function(e) {
        let parentRow = $(this).parents("tr");
        toDisplayAllCalculations(parentRow);
    });

    $('#addEditInvoice').validate({
        rules: {
            invoice_number: {
                required: true
            },
            client_id: {
                required: true,
            },
            invoice_date: {
                required: true
            },
            invoice_payment_terms: {
                required: true,
                number: true
            },
            invoice_subscription_name: {
                required: function(element) {
                    return $('#is_invoice_recurring').val() == 'on';
                } 
            },
            invoice_subscription_cycle: {
                required: function(element) {
                    return $('#is_invoice_recurring').val() == 'on';
                }
            },
            invoice_subscription_date: {
                required: function(element) {
                    return $('#is_invoice_recurring').val() == 'on';
                }
            },
            invoice_incremented_percentage: {
                required: function(element) {
                    return $('#is_invoice_rec_increment').val() == 'on';
                },
                number: true
            }
        },
        messages: {
            invoice_number: {
                required: 'Please enter invoice number'
            },
            client_id: {
                required: 'Please select client',
            },
            invoice_date: {
                required: 'Please select invoice date'
            },
            invoice_payment_terms: {
                required: 'Please enter the number of days in which the invoice will be due',
                number: 'Please enter numbers only'
            },
            invoice_subscription_name: {
                required: "Please enter subscription name"
            },
            invoice_subscription_cycle: {
                required: "Please select recurring invoice cycle"
            },
            invoice_subscription_date: {
                required: "Please select recurring invoice date"
            },
            invoice_incremented_percentage: {
                required: "Please enter next increment percentage value",
                number: "Please enter number only"
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "client_id") {
                error.appendTo("#errorToShow");
            }else if(element.attr("name") == "is_status") {
                error.appendTo("#errorToShowIsStatus");
            }else if(element.attr("name") == "invoice_subscription_cycle") {
                error.appendTo('#errorToShowCycle');
            }else {
                error.insertAfter(element);
            }
        }
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

    //Due Date 
    $('#invoice_payment_terms').blur(function(e) {
        var invoice_date = $('#invoice_date').val();
        var payment_terms = $('#invoice_payment_terms').val();
        if(payment_terms != '' && invoice_date != '') {
            getInvoiceDueDate();
        }
    });
    
    $('#invoice_date').blur(function(e) {
        var invoice_date = $('#invoice_date').val();
        var payment_terms = $('#invoice_payment_terms').val();
        if(payment_terms != '' && invoice_date != '') {
            getInvoiceDueDate();
        }
    });
    
    //All common functions
    function getProductDetails(row, product_id, check) {
        var url = base_url + 'invoices/product-details/'+product_id;
        var pro_qua = $('#product_quantity', row).val();
        if(product_id) {
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
                    if(data.success == 1) {
                        let tax_type = data.product.product_tax;
                        let product_price = data.product.product_price;
                        if(check == 1) {
                            $("#product_description", row).val(data.product.product_description);
                            $('select[name="tax_selection"]', row).val(tax_type);
                            $("#product_unit_price", row).val(product_price);
                            $('#product_quantity', row).val(pro_qua ? pro_qua :  1);
                        }
                        //set hidden fields value
                        $('#hidden_prod_quantity', row).val(pro_qua ? pro_qua :  1);
                        $('#hidden_prod_unit_price', row).val(product_price);
                        $('#hidden_prod_tax_sel', row).val(tax_type);
                        $('#hidden_product_description', row).val(data.product.product_description);
                        setTimeout(() => {
                            toDisplayAllCalculations(row);
                        }, 100)
                    }
                },
                error: function (error) {
                    console.log(error) 
                }
            });
        }
    }

    function toDisplayAllCalculations(parentRow) {
        final_total = 0;
        inclusive_tax = 0;
        inclusive_tax_amt = 0;
        item_total = 0;
        ex_product_total = 0;
        exclusiv_tax_amt = 0;
        round_exclusive_tax = 0;
        ng_item_total = 0;

        let product_id = $('#product_id', parentRow).val();
        let product_quantity = $('.product_quantity', parentRow).val();
        let product_price = $("#product_unit_price", parentRow).val();
        let tax_type = $('select[name="tax_selection"]', parentRow).val();
        let prod_desc = $('#product_description', parentRow).val();

        let res = getRowTaxCalculation(tax_type, product_price, product_quantity);
        $('#product_subtotal', parentRow).val('$ '+parseFloat(res.item_total));
        $('#product_gst', parentRow).val('$ '+parseFloat(res.gst_amount));
        $('#product_grand_total', parentRow).val('$ '+parseFloat(res.item_grand_total));

        //set hidden fields value
        $('#hidden_prod_tax_sel', parentRow).val(tax_type);
        $('#hidden_prod_quantity', parentRow).val(product_quantity);
        $('#hidden_product_subtotal', parentRow).val(res.item_total);
        $('#hidden_prod_gst', parentRow).val(res.gst_amount);
        $('#hidden_product_grand_total', parentRow).val(res.item_grand_total);
        $('#hidden_prod_id', parentRow).val(product_id);
        $('#hidden_prod_unit_price', parentRow).val(product_price);
        $('#hidden_product_description', parentRow).val(prod_desc);

        let response = getTaxCalculatedPrice();             
        $('#itemTotal').html(getPrice(response.final_item_total));
        $('#exclusiveAmt').html(getPrice(response.final_tax_amt));
        $('#finalTotal').html(getPrice(response.grand_total));
        
        $('#invoice_grand_item_total').val(response.final_item_total);
        $('#invoice_grand_gst').val(response.final_tax_amt);
        $('#product_final_total').val(response.grand_total);
    }

    function getRowTaxCalculation(tax_type, product_price, product_quantity) {
        var in_ex_gst_amount = 0;
        var final_total_final =0;
        var final_item_total = 0;
        var item_grand_total = 0;
    
        if(tax_type == 'GST Inclusive') {
            final_total = product_price * product_quantity;
            final_total_final += final_total;
            // inclusive_tax =  final_total*11/(100+11);
            inclusive_tax =  final_total / 11;
            inclusive_tax_amt = Math.round(inclusive_tax * 100) / 100;
            in_ex_gst_amount += inclusive_tax_amt;
            final_item_total += final_total - inclusive_tax_amt;
            item_grand_total = final_item_total + in_ex_gst_amount

        }else if(tax_type == 'GST'){   
            ex_product_total = product_price * product_quantity;
            final_item_total += ex_product_total;
            exclusiv_tax_amt = (ex_product_total * 10) / 100;
            round_exclusive_tax = Math.round(exclusiv_tax_amt * 100) / 100;
            in_ex_gst_amount += exclusiv_tax_amt;

            item_grand_total = final_item_total + in_ex_gst_amount
        }else if(tax_type == 'No GST') {
            ng_item_total = product_price * product_quantity;
            final_item_total += ng_item_total;
            in_ex_gst_amount = 0;
            item_grand_total = final_item_total;
        }

        let totalObj = {
            'item_total' : final_item_total,
            'gst_amount' : in_ex_gst_amount,
            'item_grand_total' : item_grand_total
        }

        return totalObj;
    }

    function getTaxCalculatedPrice() {
        var final_item_total = 0;
        var grand_total = 0;
        var final_tax_amt = 0;

        var table = $('#invoiceProductTable');
        var rows = table.find('tr:not(:hidden)');
        rows.each(function (index) {
            if(index != 0) {
                var td = $(this).find('td');
                var subtotal = $('#product_subtotal', td).val();
                var subtotal_split = subtotal.split(" ");
                var product_gst = $('#product_gst', td).val();
                var product_gst_split = product_gst.split(" ");
                let each_row_total = subtotal == '$ 0' ? 0 : parseFloat(subtotal_split[1]);
                let each_row_tax = product_gst == '$ 0' ? 0 : parseFloat(product_gst_split[1]);
                final_item_total += each_row_total;
                final_tax_amt += each_row_tax;
                grand_total = final_item_total + final_tax_amt;       
            }
        });
        let totalObj = {
            'final_item_total' : Math.round(final_item_total * 100) / 100,
            'final_tax_amt': Math.round(final_tax_amt * 100) / 100,
            'grand_total' : Math.round(grand_total * 100) / 100,
        }
        return totalObj;
    }

    function getInvoiceDueDate() {
        var invoice_date = $('#invoice_date').val();
        var payment_terms = $('#invoice_payment_terms').val();
        var data = {
            invoice_date: invoice_date,
            payment_terms: payment_terms
        }
        var url = base_url + 'invoices/invoice-due-date';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: url,
            dataType: 'json',
            type: "POST",
            data:data,
            success: function (res) {
                if(res.success == 1) {
                    $('#invoice_due_date').val(res.due_date);
                }
            },
            error: function (error) {
                console.log(error); 
            }
        });
    }
  
    var tableRows = $('#invoiceProductTable').find('tbody > tr');
    tableRows.each(function (index) {
        var parentRow = $(this);
        $("#product_description", parentRow).on('keyup', function() {
            var text = $(this).val();
            $('#hidden_product_description', parentRow).val(text);
        });
        $("#product_unit_price", parentRow).on('keyup', function() {
            var pro_unit_price = $(this).val();
            $('#hidden_prod_unit_price', parentRow).val(pro_unit_price);
        });        
    });

    //Set client number
    $('#client_id').on('change', function() {
        var client_number = $(this).find(':selected').data('client');
        $('#client_number').val(client_number);
    })

    //Edit Invoice Client Number
    var customer = $('#client_id').find(':selected').data('client');
    $('#client_number').val(customer);
});