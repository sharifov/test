favicon = new Favico({
    animation : 'slide'
});

PNotify.prototype.options.styling = "bootstrap3";
// PNotify.defaults.styling = 'bootstrap3'; // Bootstrap version 3
// PNotify.defaults.icons = 'bootstrap3'; // glyphicons

PNotify.desktop.permission();

ion.sound({
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
})

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