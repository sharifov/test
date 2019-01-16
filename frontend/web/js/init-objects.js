favicon = new Favico({
    animation : 'slide'
});

PNotify.prototype.options.styling = "bootstrap3";
PNotify.desktop.permission();

ion.sound({
    sounds: [
        {name: "bell_ring"},
        {name: "door_bell"},
        {name: "button_tiny"}
    ],
    path: '/js/sounds/',
    preload: true,
    multiplay: true,
    volume: 0.8
});