$(document).ready(function () {
    $('.fos_user_resetting_request').validate({
        rules: {
            username: {
                required: true,
                minlength: 3
            }
        },
        errorElement: 'ul',
        errorLabelContainer: '#msgbox',
        wrapper: 'li',
        messages: {
            username: "This field must contain minimum 3 characters"
        }
    });

    $('#msgbox').hide();
});