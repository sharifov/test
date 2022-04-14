function widgetStatus(selector, updateStatusUrl) {

    let url = updateStatusUrl;

    var parent = '.status-confirmation';

    var state = {
        status: $(selector).attr('checked') ? true : false,
        shown: false
    };

    function node(status) {
        return ('<div class="status-confirmation-tooltip">'+
            '<span>Switch to <i class="' + (status ? 'occupied' : 'online') +'">' + (status ? 'OFF' : 'ON') + '</i> ?</span>'+
            '<div class="status-action-group">'+
            '<a href="#" data-status-action="false">NO</a>'+
            '<a href="#" data-status-action="true"><i class="fa fa-check"></i></a>'+
            '</div>'+
            '</div>');
    }

    function setPhoneStatusOn() {
        $('#pw_status_name').text('ON');
    }

    function setPhoneStatusOff() {
        $('#pw_status_name').text('OFF');
    }

    function handleChange(btn) {

        let action = btn.attr('data-status-action');

        if (action === 'true') {
            btn.html('<i class="fa fa-spinner fa-spin"></i>');

            let type_id = 1;
            if (state.status) {
                type_id = 2;
            }

            $.ajax({
                type: 'post',
                data: {'type_id': type_id},
                url: url
            })
            .done(function(data) {
                let status = true;
                if (type_id === 2) {
                    status = false;
                    setPhoneStatusOff()
                } else {
                    setPhoneStatusOn()
                }
                $(selector).prop('checked', status);
                state.status = status;

            })
            .fail(function () {
                createNotifyByObject({title: "Change status", type: "error", text: "Server error", hide: true});
            })
            .always(function() {
                btn.html('<i class="fa fa-check"></i>');
                if (state.shown) {
                    $('.status-confirmation-tooltip').detach()
                }
                state.shown = false;
            });
        } else {
            if (state.shown) {
                $('.status-confirmation-tooltip').detach()
            }
            state.shown = false;
        }
    }

    $(document).on('click', '[data-status-action]', function(e) {
        e.preventDefault();
        handleChange($(this));
    });

    $(document).on('click', selector, function(e){
        e.preventDefault();

        if (!state.shown) {
            state.shown = true;
            $(parent).append(node(state.status));
        }
    });

    $(document).on('click', '.phone-widget', function(e) {
        if (state.shown && !$(e.target).closest('.number-toggle').length) {
            $('.status-confirmation-tooltip').detach();
            state.shown = false;
        }
    });

    return {
        getStatus: function() {
            switch (state.status) {
                case true:
                    return 1;

                case false:
                    return 2;
            }
        },
        setStatus: function(status) {
            if (status === 1) {
                state.status = true;
                setPhoneStatusOn();
            } else {
                state.status = false;
                setPhoneStatusOff();
            }
            $(parent).html('');
            $('.status-confirmation-tooltip').detach();
            state.shown = false;
            $(selector).prop('checked', state.status);
        }
    }

}
