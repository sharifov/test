favicon = new Favico({
    animation : 'slide'
});
faviconChat = new Favico({
    animation : 'slide',
    position : 'up',
    bgColor : '#5CB85C',
    textColor : '#ff0'
});



// PNotify.defaultModules.set(PNotifyBootstrap4, {});
// PNotify.defaultModules.set(PNotifyFontAwesome5, {});
PNotify.defaults.styling = 'angeler';
PNotify.defaults.icons = 'angeler';
// PNotify.defaults.addClass = 'angeler-extended';
if (typeof window.stackPaginate === 'undefined') {
    window.stackPaginate = new PNotify.Stack({
        dir1: 'down',
        dir2: 'left',
        firstpos1: 25,
        firstpos2: 25,
        modal: false
    });
}

PNotifyDesktop.permission();

/*ion.sound({
    sounds: [
        {name: "bell_ring", volume: 0.2},
        {name: "door_bell", volume: 0.05},
        {name: "button_tiny", volume: 0.1},
        {name: "incoming_call", volume: 0.2},
    ],
    path: '/js/sounds/',
    preload: true,
    multiplay: true,
    volume: 0.8
})*/

function createNotify (title, message, type) {
    if (type === 'warning') {
        type = 'notice';
    }
    PNotify.alert({
        title: title,
        text: message,
        stack: window.stackPaginate,
        type: type,
        destroy: true,
        icon: true,
        modules: new Map([
            ...PNotify.defaultModules,
            [PNotifyPaginate, {}],
        ]),
        delay: 2000,
        mouse_reset: false,
        textTrusted: true
    });
}

function createNotifyByObject(obj)
{
    if (obj.type === 'warning') {
        obj.type = 'notice';
    }
    let options = {
        stack: window.stackPaginate,
        destroy: true,
        icon: true,
        modules: new Map([
            ...PNotify.defaultModules,
            [PNotifyPaginate, {}],
        ]),
        delay: 2000,
        mouse_reset: false,
        textTrusted: true
    };
    options = $.extend(true, options, obj);
    PNotify.alert(options);
}

function createDesktopNotify(id, title, message, type, desktopMessage)
{
    PNotify.alert({
        title: title,
        text: message,
        type: type,
        destroy: true,
        modules: new Map([
            ...PNotify.defaultModules,
            [PNotifyDesktop, {
                fallback: true,
                text: desktopMessage,
                tag: 'notification-popup-showed-id-' + id
            }],
        ]),
        delay: 4000,
        mouse_reset: false,
        textTrusted: true
    });
}

$("document").ready(function(){
    $(document).on('click', '.btn-recording_url', function() {
        let modal = $('#modal-lg');
        let source_src = $(this).data('source_src');
        let rateStr = '<div class="col-md-1"><div class="form-group"><label class="control-label" for="rate_audio_controls">Playback Rate</label> <input type="number" min="0.8" max="5" step="0.1" class="form-control" id="rate_audio_controls" name="rate_audio_controls" value="1"></div></div>';

        modal.find('.modal-body').html(rateStr + '<div class="col-md-12"><audio preload="auto" controls="controls" controlsList="nodownload" autoplay="true" id="audio_controls" style="width: 100%;"><source src="'+ source_src +'" type="audio/mpeg"></audio></div>');
        modal.find('.modal-title').html('Play Call record');
        modal.modal('show');
    });

    $(document).on('change', '#rate_audio_controls', function() {
        let myaudio = document.getElementById("audio_controls");
        myaudio.playbackRate = $(this).val();
    });

    $('#modal-lg').on('hidden.bs.modal', function () {
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
    });
});