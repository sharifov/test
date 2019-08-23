favicon = new Favico({
    animation : 'slide'
});

PNotify.prototype.options.styling = "bootstrap3";
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
});