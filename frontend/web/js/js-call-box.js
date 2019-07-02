$(document).on('click', '#prime', function () {
    toggleFab();
});


//Toggle call_box and links
function toggleFab() {
    //$('.prime').toggleClass('fa-list');
    $('.prime').toggleClass('fa-close');
    $('.prime').toggleClass('is-active');
    $('.prime').toggleClass('is-visible');
    $('#prime').toggleClass('is-float');
    $('.call_box').toggleClass('is-visible');
    $('.fab').toggleClass('is-visible');
}

$(document).on('click', '#call_box_first_screen', function (e) {
    hideCallBox(2);
});

/*$('#call_box_second_screen').click(function (e) {
    hideCallBox(2);
});*/

/*$('#chat_third_screen').click(function(e) {
    hideChat(0);
});*/

$(document).on('click', '#call_box_third_screen', function(e) {
    hideCallBox(0);
});


$(document).on('click', '#call_box_fullscreen_loader', function (e) {
    $('.fullscreen').toggleClass('fa-window-maximize');
    $('.fullscreen').toggleClass('fa-window-restore');
    $('.call_box').toggleClass('call_box_fullscreen');
    $('.fab').toggleClass('is-hide');
    $('.header_img').toggleClass('change_img');
    $('.img_container').toggleClass('change_img');
    $('.call_box_header').toggleClass('call_box_header2');
    $('.fab_field').toggleClass('fab_field2');
    $('.call_box_converse').toggleClass('call_box_converse2');
    //$('#call_box_converse').css('display', 'none');
    // $('#call_box_body').css('display', 'none');
    // $('#call_box_form').css('display', 'none');
    // $('.call_box_login').css('display', 'none');
    // $('#call_box_fullscreen').css('display', 'block');
});

function hideCallBox(hide) {
    switch (hide) {
        case 0:
            $('#call_box_converse').css('display', 'none');
            //$('#call_box_body').css('display', 'none');
            $('#call_box_form').css('display', 'none');
            $('.call_box_login').css('display', 'block');
            $('.call_box_fullscreen_loader').css('display', 'none');
            $('#call_box_fullscreen').css('display', 'none');
            break;
        case 1:
            $('#call_box_converse').css('display', 'block');
            //$('#call_box_body').css('display', 'none');
            $('#call_box_form').css('display', 'none');
            $('.call_box_login').css('display', 'none');
            $('.call_box_fullscreen_loader').css('display', 'block');
            break;
        case 2:
            $('#call_box_converse').css('display', 'none');
            //$('#call_box_body').css('display', 'block');
            $('#call_box_form').css('display', 'none');
            $('.call_box_login').css('display', 'none');
            $('.call_box_fullscreen_loader').css('display', 'block');
            break;
    }
}


function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}

function deleteCookie(name) {
    setCookie(name, "", {expires: -1})
}


//toggleFab();
//hideCallBox(0);