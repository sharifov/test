function notificationInit(data) {
    console.log('notificationInit.start');
    console.log(data);

    let command = null;

    try {
        command = data['command'];
    } catch (error) {
        console.error('Invalid data command');
        console.error(error);
        return;
    }

    if (command === 'add') {
        try {
            notificationAddMessage(
                data['id'],
                data['url'],
                data['title'],
                data['time'],
                data['message'],
                data['type'],
                data['popup'],
                data['notifyMessage'],
                data['notifyDesktopMessage']
            );
        } catch (error) {
            console.error('Invalid data for command = add');
            console.error(error);
            return;
        }
    } else if (command === 'delete') {
        try {
            notificationDeleteMessage(data['id']);
        } catch (error) {
            console.error('Invalid data for command = delete');
            console.error(error);
            return;
        }
    } else if (command === 'delete_all') {
        notificationDeleteAllMessages();
    }

    notificationUpdateTime();

    console.log('notificationInit.end');
}

function notificationDeleteAllMessages() {
    let isDeleted = false;
    $( "#notification-menu li").each(function(e) {
        let messageId = $(this).data('id');
        if (messageId) {
            isDeleted = true;
            $(this).remove();
        }
    });
    if (!isDeleted) {
        console.error('Messages not found');
    } else {
        notificationCounterReset();
        console.log('Messages was deleted');
    }
}

function notificationDeleteMessage(id) {
    let isDeleted = false;
    $("#notification-menu li").each(function(e) {
        let messageId = $(this).data('id');
        if (messageId && messageId === id) {
            isDeleted = true;
            $(this).remove();
            notificationCounterDecrement();
            console.log('Message Id: ' + id + ' was deleted');
            return false;
        }
    });
    if (!isDeleted) {
        console.error('Message Id: ' + id + ' not found');
    }
}

function notificationIsExist(id) {
    let isExist = false;
    $("#notification-menu li").each(function(e) {
        let messageId = $(this).data('id');
        if (messageId && messageId === id) {
            isExist = true;
            return false;
        }
    });
    return isExist;
}

function notificationAddMessage(id, url, title, time, message, type, popup, notifyMessage, notifyDesktopMessage) {
    if (notificationIsExist(id)) {
        console.error('Message Id: ' + id + ' already exist on UI list');
        return;
    }

    let text = '<li data-id="' + id + '"> '
        + '<a href="javascript:;" onclick="notificationShow(this);" id="notification-menu-element-show" data-title="' + title + '" data-id="' + id + '">'
        + '<span class="glyphicon glyphicon-info-sign"> </span> '
        + '<span>'
        + '<span>' + title + '</span>'
        + '<span class="time" data-time="' + time + '">' + time + '</span>'
        + '</span>'
        + '<span class="message">' + message +'<br></span>'
        + '</a>'
        + '</li>';

    $("#notification-menu").prepend(text);
    notificationCounterIncrement();
    $( "#notification-menu li").each(function(e) {
        //remove 10th  element
        if (e === 10) {
            let messageId = $(this).data('id');
            if (messageId) {
                $(this).remove();
                console.log('Message Id: ' + messageId + ' was delete from UI list');
            }
        }
    });
    if (popup) {
        notificationPNotify(id, type, title, notifyMessage, notifyDesktopMessage);
    }
    console.log('Message Id: ' + id + ' was added');
}

function notificationCounterIncrement() {
    $(".notification-counter").each(function() {
        let count = $(this).text();
        if (count) {
            count = parseInt(count);
            count++;
        } else {
            count = 1;
        }
        $(".notification-counter").text(count);
        return false;
    });
}

function notificationCounterDecrement() {
    $(".notification-counter").each(function() {
        let count = $(this).text();
        if (count) {
            count = parseInt(count);
            if (count > 1) {
                count--;
            } else {
                count = '';
            }
        } else {
            count = '';
        }
        $(".notification-counter").text(count);
        return false;
    });
}

function notificationCounterReset() {
    $(".notification-counter").text('');
}

function notificationUpdateTime() {
    $( "#notification-menu li .time").each(function() {
        $(this).text(notificationTimeDifference(new Date(), new Date($(this).data('time') * 1000)));
    });
}

function notificationTimeDifference(current, previous) {
    let msPerMinute = 60 * 1000;
    let msPerHour = msPerMinute * 60;
    let msPerDay = msPerHour * 24;
    let msPerMonth = msPerDay * 30;
    let msPerYear = msPerDay * 365;

    let elapsed = current - previous;

    if (elapsed < msPerMinute) {
        return Math.round(elapsed/1000) + ' seconds ago';
    } else if (elapsed < msPerHour) {
        return Math.round(elapsed/msPerMinute) + ' minutes ago';
    } else if (elapsed < msPerDay ) {
        return Math.round(elapsed/msPerHour ) + ' hours ago';
    } else if (elapsed < msPerMonth) {
        return Math.round(elapsed/msPerDay) + ' days ago';
    } else if (elapsed < msPerYear) {
        return Math.round(elapsed/msPerMonth) + ' months ago';
    } else {
        return Math.round(elapsed/msPerYear ) + ' years ago';
    }
}

function notificationPNotify(id, type, title, message, desktopMessage) {
    new PNotify({
        type: type,
        title: title,
        text: message,
        icon: true,
        desktop: {
            desktop: true,
            fallback: true,
            text: desktopMessage,
            tag: 'notification-popup-showed-id-' + id
        },
        delay: 10000,
        mouse_reset: false,
        hide: true,
    });
    soundNotification();
}

function notificationCount(count) {
    $(".notification-counter").text(count);
}

function notificationShow(element) {
    let url = '/notifications/view2?id=' + $(element).data('id');
    let title = $(element).data('title');
    let modal = $('#modal-lg');
    $.get(url,
        function (data) {
            modal.find('.modal-title').html(title);
            modal.find('.modal-body').html(data);
            modal.modal();
        }
    );
    return false;
}
