$(document).ready(function () {
    $('#resetPasswordForm').validate({
        rules: {
            password: {
                required: true
            },
            confirm_password: {
                required: true,
                equalTo: "#password"
            },
        },
        messages: {
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