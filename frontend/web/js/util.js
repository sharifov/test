function validatePriceField(element, key) {
    var allowedKeys = [
        8, 37, 39, 46, 110, 190,
        48, 49, 50, 51, 52, 53, 54, 55, 56, 57,
        96, 97, 98, 99, 100, 101, 102, 103, 104, 105
    ];
    if (element.hasClass('mark-up')) {
        allowedKeys.push(109);
        allowedKeys.push(189);
    }

    if ($.inArray(key, allowedKeys) == -1) {
        element.val(element.val().replace(/[^0-9\.]/g, ''));
        event.preventDefault();
    } else {
        var explode = element.val().split(".");
        if (explode.length > 2) {
            element.val(explode[0] + '.' + explode[1]);
        }
        if (element.val().split('-').length > 2) {
            element.val('-' + element.val().replace(/\-/g, ''));
        }
    }
}

$('.sync').click(function (e) {
    e.preventDefault();
    $.get( $(this).data('url'), function( data ) { });
});

function UpdateClock(timeZone, element) {
    var d = new Date();
    //get the timezone offset from local time in minutes
    var tzDifference = timeZone * 60 + d.getTimezoneOffset();
    //convert the offset to milliseconds, add to targetTime, and make a new Date
    var offset = tzDifference * 60 * 1000;

    var tDate = new Date(new Date().getTime() + offset);
    var in_hours = tDate.getHours();
    var in_minutes = tDate.getMinutes();
    var in_seconds = tDate.getSeconds();

    if (in_minutes < 10)
        in_minutes = '0' + in_minutes;
    if (in_seconds < 10)
        in_seconds = '0' + in_seconds;
    if (in_hours < 10)
        in_hours = '0' + in_hours;

    var timeStr = ""
        + in_hours + ":"
        + in_minutes;
    $('#' + element).text(timeStr);
}

function setClienTime() {
    $('.sale-client-time').each(function (index) {
        var element = $(this).attr('id');
        var timeZone = $(this).data('offset');
        UpdateClock(timeZone, element);
        setInterval(function () {
            UpdateClock(timeZone, element);
        }, 500);
    });
}

setClienTime();
