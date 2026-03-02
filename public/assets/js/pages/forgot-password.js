$(document).ready(function () {
    $('#forgotPasswordForm').validate({
        rules: {
            email: {
            	email:true,
                required: true
            }
        },
        messages: {
            email: {
                required: 'Please enter email',
                email: 'Please enter valid email'
            }
        }
    });
});