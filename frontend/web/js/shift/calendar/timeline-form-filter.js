;(function (document, window, $) {
    'use strict';
    var App = window.App || {};

    let btn;
    let btnHtml;
    let $collapsedResourcesInput = $('#collapsedResources');

    function TimelineFormFilter(formElementId) {
        this.formElementId = formElementId;
        btn = $('#filter-calendar-form-btn');
        btn.on('click', submitFormClick.bind(this));
    }

    TimelineFormFilter.prototype.init = function (timeline) {
        this.timeline = timeline;
    };

    TimelineFormFilter.prototype.getQueryString = function () {
        if (this.timeline) {
            let collapsed = [];
            this.timeline.timeline.props.resources.forEach(function (i, e) {
                if (!i.collapsed) {
                    collapsed.push(i.mainId || null);
                }
            });
            $collapsedResourcesInput.val(collapsed.join(','));
        }
        let form = document.getElementById(this.formElementId);
        let formData = new FormData(form);
        formData.delete('_csrf-frontend');
        return new URLSearchParams(formData).toString();
    };

    TimelineFormFilter.prototype.disableSubmitBtn = function () {
        let btn = $('#filter-calendar-form-btn');
        btnHtml = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm"></span> Loading').prop("disabled", true);
    };

    TimelineFormFilter.prototype.enableSubmitBtn = function () {
        btn.html(btnHtml).prop("disabled", false);
    };

    function submitFormClick(e)
    {
        e.preventDefault();
        btnHtml = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm"></span> Loading').prop("disabled", true);
        let queryString = this.getQueryString();
        // $('#calendar-wrapper').append(loaderTemplate());
        this.timeline.enableLoader();
        this.timeline.getCalendarEvents(queryString)
            .then(() => {
                window.history.replaceState(null, null, '?' + queryString + '&appliedFilter=1');
            })
            .finally(function() {
                btn.html(btnHtml).prop("disabled", false);
                this.timeline.disableLoader();
            }.bind(this));
    }

    App.TimelineFormFilter = TimelineFormFilter;
    window.App = App;
})(document, window, $);