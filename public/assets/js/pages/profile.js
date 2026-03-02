$(document).ready(function () {
    $('#editProfileForm').validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
            	email: true,
                required: true
            },
            phone_number: {
            	number: true,
                required: true
            },
            confirm_password: {
                required: function(element) {
                    return $('#password').val() != '';
                },
                equalTo: "#password"
            },
        },
        messages: {
            first_name: {
                required: 'Please enter first name'
            },
            last_name: {
                required: 'Please enter first name'
            },
            email: {
                required: 'Please enter email',
                email: 'Please enter valid email'
            },
            phone_number: {
            	number: 'Please enter digit only',
                required: 'Please enter phone number'
            },
            confirm_password: {
                required: 'Please enter confirm password',
                equalTo: 'Password and confirm password should be same'
            }
        }
    });
});