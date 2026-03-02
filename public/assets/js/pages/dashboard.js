$(document).ready(function () {
    $('#unPaidInvoices').on('click', '.viewNote', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'invoices/view-invoice-note/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                if(data.invoice_notes) {
                    $('#invoice_notes').html(data.invoice_notes);
                }else {
                    $('#invoice_notes').html('No notes are added for this invoice.');
                }
                $('#view_invoice_notes').modal('show');
            }
        });
    });

    $('#unPaidInvoices').on('click', '.sendEmail', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'invoices/send-email-to-client/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success == 1) {
                swal("Success! Email sent successfully!", {
                    icon: "success",
                });
            }else {
                swal("Oops! Error sending in email.", {
                    icon: "error",
                });
            }
        });
    });

    $('#recurringInvoiceTable').DataTable({
        "aaSorting": []
    });
    
    $('#unPaidInvoices').DataTable({
        "aaSorting": [],
        'columnDefs': [{
            'targets': [5],
            'orderable': false,
        }]
    });

    $('#quotesTable').DataTable({
      
    });
});