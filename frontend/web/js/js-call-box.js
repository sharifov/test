$('#prime').on('click', function () {
    toggleFab();
});


//Toggle call_box and links
function toggleFab() {
    $('.prime').toggleClass('fa-phone');
    $('.prime').toggleClass('fa-close');
    $('.prime').toggleClass('is-active');
    $('.prime').toggleClass('is-visible');
    $('#prime').toggleClass('is-float');
    $('.call_box').toggleClass('is-visible');
    $('.fab').toggleClass('is-visible');
}

$('#call_box_first_screen').on('click', function (e) {
    hideCallBox(2);
});

/*$('#call_box_second_screen').click(function (e) {
    hideCallBox(2);
});*/

/*$('#chat_third_screen').click(function(e) {
    hideChat(0);
});*/

$('#call_box_third_screen').on('click', function(e) {
    hideCallBox(0);
});


$('#call_box_fullscreen_loader').on('click', function (e) {
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
            $('#call_box_body').css('display', 'none');
            $('#call_box_form').css('display', 'none');
            $('.call_box_login').css('display', 'block');
            $('.call_box_fullscreen_loader').css('display', 'none');
            $('#call_box_fullscreen').css('display', 'none');
            break;
        case 1:
            $('#call_box_converse').css('display', 'block');
            $('#call_box_body').css('display', 'none');
            $('#call_box_form').css('display', 'none');
            $('.call_box_login').css('display', 'none');
            $('.call_box_fullscreen_loader').css('display', 'block');
            break;
        case 2:
            $('#call_box_converse').css('display', 'none');
            $('#call_box_body').css('display', 'block');
            $('#call_box_form').css('display', 'none');
            $('.call_box_login').css('display', 'none');
            $('.call_box_fullscreen_loader').css('display', 'block');
            break;
    }
}

var tProgress = 0;

function inProgressStart() {
    tProgress = 1;
    inProgressGo();
}

function inProgressGo() {
    var i, n, s = '';
    for (i = 0; i < 10; i++) {
        n = Math.floor(Math.sin((Date.now()/200) + (i/2)) * 4) + 4;
        s += String.fromCharCode(0x2581 + n);
    }

    if(tProgress > 0) {
        window.location.hash = s;
        setTimeout(inProgressGo, 50);
    } else {
        window.location.hash = '';
    }
}

function inProgressStop() {
    tProgress = 0;
    window.location.hash = '';
}

//toggleFab();
hideCallBox(0);