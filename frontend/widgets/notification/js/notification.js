function notificationInit(data) {
    console.log('notificationInit.start');
    console.log(data);
    try {
        var command = data['command'];
        var message = data['message'];
    } catch (error) {
        console.error('Invalid data on notificationInit');
        console.error(data);
        return;
    }

    if (command === 'add') {
        notificationAdd(message);
    }
}

function notificationAdd(message) {
    $("#notification-menu").prepend('<li>we</li>');
}
