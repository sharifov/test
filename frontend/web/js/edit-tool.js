function editToolSend(form, modalId, script, header, isNotify) {
    $.ajax({
        type: $(form).attr('method'),
        url: $(form).attr('action'),
        data: $(form).serializeArray(),
        dataType: 'json',
        success: function(data) {
            $('#' + modalId).modal('toggle');
            if (data.success) {
                if (isNotify) {
                    let text = 'Success';
                    if (data.text) {
                        text = data.text;
                    }
                    createNotifyByObject({title: header, text: text, type: 'info'});
                }
            } else {
                if (isNotify) {
                    let text = 'Error. Try again later.';
                    if (data.text) {
                        text = data.text;
                    }
                    createNotifyByObject({title: header, text: text, type: 'error'});
                }
            }
            if (script) {
                eval(script);
            }
        },
        error: function (error) {
            $('#' + modalId).modal('toggle');
            if (isNotify) {
                createNotifyByObject({title: 'Error', text: 'Internal Server Error. Try again later.', type: 'error'});
            }
        }
    })
}
