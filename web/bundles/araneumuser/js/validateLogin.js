$(document).ready(function () {
    $('#loginForm').validate({
        rules: {
            _username: {
                required: true,
                minlength: 3

            },
            _password: {
                required: true,
                minlength: 3
            }
        },
        errorElement: 'ul',
        errorLabelContainer: '#msgbox',
        wrapper: 'li',
        messages: {
            _username: "Field name must contain minimum 3 characters",
            _password: "Field password required and must contain minimum 3 characters"
        }
    });

    $('#msgbox').hide();
});
