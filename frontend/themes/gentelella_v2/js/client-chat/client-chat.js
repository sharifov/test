;( function (window) {
    'use strict';
    window._cc_apply_filter = function (channelId, primaryUrl, status, dep, project, group, read, agentId, createdDate) {
        let btn = $("#btn-load-channels");
        let params = new URLSearchParams(window.location.search);

        let url = primaryUrl + "?status=" + status + "&group=" + group + "&read=" + read + "&agentId=" + agentId + "&createdDate=" + createdDate;
        if (dep > 0) {
            url = url + "&dep=" + dep;
        }
        if (project > 0) {
            url = url + "&project=" + project;
        }
        if (channelId > 0) {
            url = url + "&channelId="+channelId;
        }

        $.ajax({
            type: "post",
            url: url,
            dataType: "json",
            cache: false,
            data: {page: 1, channelId: params.get("channelId") | channelId},
            beforeSend: function () {
                $("#_channel_list_wrapper").append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"> </i></div></div>');
            },
            success: function (data) {
                $("._cc-list-wrapper").html(data.html);
                if (data.html) {
                    btn.html("Load more").removeAttr("disabled").removeClass("disabled").attr("data-page", data.page);
                } else {
                    btn.html("All conversations are loaded").prop('disabled', true).addClass('disabled');
                }
                params.set('page', 1);
                params.set('channelId', channelId);
                params.set('status', status);
                params.set('dep', dep);
                params.set('project', project);
                params.set('group', group);
                params.set('read', read);
                params.set('agentId', agentId);
                params.set('createdDate', createdDate);
                window.history.replaceState({}, '', primaryUrl+"?"+params.toString());
            },
            complete: function () {
                $("#_channel_list_wrapper").find('#_cc-load').remove();
            }
        });
    };

    window.updateClientChatFilter = function(formId, formName, loadChannelsUrl) {
        let filterParams = getClientChatFilterParams(formId);
        let urlParams = new URLSearchParams(window.location.search);

        urlParams.delete('chid');
        urlParams.delete('page');

        let otherUrlParams = new URLSearchParams();

        urlParams.forEach(function(value, key) {
            if (key.indexOf(formName) !== 0) {
                otherUrlParams.set(key, value);
            }
        });

        window.history.replaceState({}, '', loadChannelsUrl + '?' + filterParams + '&' + otherUrlParams.toString());

        $('.cc_btn_read_filter').removeClass('active');
        $('._rc-iframe').hide();
        $('#_client-chat-info').html('');
        $('#_client-chat-note').html('');
        pjaxReload({container: '#pjax-client-chat-channel-list'});
    };

    function getClientChatFilterParams(formId) {
        return $("#" + formId).serialize();
    }

    window.getClientChatLoadMoreUrl = function(formId, formName, loadChannelsUrl) {
        let filterParams = getClientChatFilterParams(formId);
        let urlParams = new URLSearchParams(window.location.search);

        urlParams.delete('page');

        let otherUrlParams = new URLSearchParams();
        urlParams.forEach(function(value, key) {
            if (key.indexOf(formName) !== 0) {
                otherUrlParams.set(key, value);
            }
        });

        return loadChannelsUrl + '?' + filterParams + '&' + otherUrlParams.toString();
    };

})(window);