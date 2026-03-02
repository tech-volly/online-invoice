$(document).ready(function () {

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
        e.preventDefault();
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
       
    $('#addEditClient').validate({
        
        rules: {
            client_business_name: {
                required: true
            },
            client_first_name: {
                required: true
            },
            client_last_name: {
                required: true 
            },
            client_mobile: {
                required: true,
                //number: true,
                pattern:/^[0-9\s()]*$/
                //customPattern: true
            },
            client_email: {
                required: true
            },
            is_status: {
                required: true
            },
            contact_telephone:{
                pattern:/^[0-9\s()]*$/
            }
        },
        messages: {
            client_business_name: {
                required: 'Please enter business name'
            },
            client_first_name: {
                required: 'Please enter first name'
            },
            client_last_name: {
                required: 'Please enter last name'
            },
            client_mobile: {
                required: 'Please enter mobile number',
                 pattern: 'Please enter valid number'
            },
            client_email: {
                required: 'Please enter email'
            },
            is_status: {
                required: 'Please select status'
            },
            contact_telephone:{
                pattern: 'Please enter valid telephone'
            }
        }
    });

    //CKEDITOR
    ClassicEditor.create( document.querySelector( '.ckeditor' ), {
        height : '350px',
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed']
    }).catch( error => {
        console.error( error );
    });

});