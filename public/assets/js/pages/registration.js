$(document).ready(function () {
    $('#registrationForm').validate({
        rules: {
            name: {
                required: true
            },
            email: {
            	email:true,
                required: true
            },
            password: {
                required: true
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            },
        },
        messages: {
            name: {
                required: 'Please enter name'
            },
            email: {
                required: 'Please enter email',
                email: 'Please enter valid email'
            },
            password: {
                required: 'Please enter password'
            },
            confirm_password: {
                required: 'Please enter confirm password',
                equalTo: 'Password and confirm password should be same'
            }
        }
    });
});