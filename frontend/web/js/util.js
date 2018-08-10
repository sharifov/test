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