;( function (window) {
    'use strict';
    window._cc_apply_filter = function (selectedChannel, primaryUrl, selectedTab, selectedDep, selectedProject) {
        let btn = $("#btn-load-channels");
        let params = new URLSearchParams(window.location.search);
        let tab = selectedTab;
        let dep = selectedDep;
        let project = selectedProject;

        let url = primaryUrl + "?tab=" + tab;
        if (dep > 0) {
            url = url + "&dep=" + dep;
        }
        if (project > 0) {
            url = url + "&project=" + project;
        }
        if (selectedChannel > 0) {
            url = url + "&channelId="+selectedChannel;
        }

        $.ajax({
            type: "post",
            url: url,
            dataType: "json",
            cache: false,
            data: {page: 1, channelId: params.get("channelId") | selectedChannel},
            beforeSend: function () {
                $("#_channel_list_wrapper").append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
            },
            success: function (data) {
                $("._cc-list-wrapper").html(data.html);
                if (data.html) {
                    btn.html("Load more").removeAttr("disabled").removeClass("disabled").attr("data-page", data.page);
                } else {
                    btn.html("All conversations are loaded").prop('disabled', true).addClass('disabled');
                }
                params.set('page', 1);
                params.set('channelId', selectedChannel);
                params.set('dep', dep);
                params.set('project', project);
                window.history.replaceState({}, '', primaryUrl+"?"+params.toString());
            },
            complete: function () {
                $("#_channel_list_wrapper").find('#_cc-load').remove();
            }
        });
    }
})(window);