jQuery(document).ready(function ($) {

    jQuery('#check').submit(function (e) {

        e.preventDefault();
        var data = {
            url: jQuery('#check_url').val(),
            platform: jQuery("#check_platform_list").val()
        };

        if (doSubmit) {
            jQuery.ajax({
                method: 'GET',
                url: rest_object.api_url + 'vbsso-check/',
                data: data,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', rest_object.api_nonce);
                },
                success: function (response) {
                    console.log(response);
                    jQuery('#check-result').html(response);
                },
                fail: function (response) {
                    console.log(response);
                    jQuery('#check-result').html(response);
                }
            })
        }
    });


});

var RC2KEY = '6Lf5bK4UAAAAAFL8l_iQ6jk_18xSGzmoFO6GFx1g',
    doSubmit = false;

function reCaptchaVerify(response) {
    if (response === document.querySelector('.g-recaptcha-response').value) {
        doSubmit = true;
    }
}

function reCaptchaCallback() {
    /* this must be in the global scope for google to get access */
    grecaptcha.render('g-recaptcha', {
        'sitekey': RC2KEY,
        'callback': reCaptchaVerify
    });
}
