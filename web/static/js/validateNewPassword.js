$(document).ready(function () {
    $('#new_password_form').validate({
        rules: {
            'fos_user_resetting_form[new][first]': {
                required: true,
                minlength: 3
            },
            'fos_user_resetting_form[new][second]': {
                required: true,
                minlength: 3,
                equalTo: "#fos_user_resetting_form_new_first"
            }
        },
        errorElement: 'p',
        errorLabelContainer: '#msgbox',
        messages: {
            'fos_user_resetting_form[new][first]': "Password must contain 3 characters minimum",
            'fos_user_resetting_form[new][second]': "Password repeat must contain 3 characters minimum"
        }
    });

    $('#msgbox').hide();
});