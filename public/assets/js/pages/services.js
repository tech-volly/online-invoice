$(document).ready(function () {
    $('.deleteServiceBtn').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'services/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this service details!",
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
                        $('#servicerow_' + id).remove();
                        swal("Deleted! Service has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Service. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('#addEditService').validate({
        rules: {
            service_name: {
                required: true
            },
            category_id: {
                required: true
            },
            service_unit_price: {
                required: true,
                number: true
            },
            service_tax1: {
                required: true,
            },
            service_tax2: {
                required: true
            },
            service_description: {
                required: true
            },
            service_internal_notes: {
                required: true
            },
            is_status: {
                required: true
            }
        },
        messages: {
            service_name: {
                required: 'Please enter service name'
            },
            category_id: {
                required: 'Please select service category'
            },
            service_unit_price: {
                required: 'Plese enter service retail price',
                number: 'Please enter digit only'
            },
            service_tax1: {
                required: 'Please select tax type',
            },
            service_tax2: {
                required: 'Please select tax type',
            },
            service_description: {
                required: 'Please enter service description'
            },
            service_internal_notes: {
                required: 'Please enter service internal notes'
            },
            is_status: {
                required: 'Please select status'
            }
        },
        
    });
});