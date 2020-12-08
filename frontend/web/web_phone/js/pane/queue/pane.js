function PhoneWidgetPaneQueue(initQueues) {
    let queues = initQueues;
    let self = this;
    let filterToggle = '.call-filter__toggle';
    let activeQueue = null;

    $(document).on('click', filterToggle, function (e) {
        e.preventDefault();
        let type = $(this).attr('data-call-filter');

        switch (type) {
            case 'active':
                activeShow();
                break;
            case 'direct':
                directShow();
                break;
            case 'general':
                generalShow();
                break;
        }

        $(filterToggle).removeClass('is-checked');
        $(this).addClass('is-checked');
        clearIndicators($(this));
        self.show();
        $('[data-toggle-tab]').removeClass('is_active');
    });

    $(document).on('click', '.widget-line-overlay__show-all-queues', function (e) {
        e.preventDefault();
        $(filterToggle).addClass('is-checked');
        clearIndicators($(this));
        allShow();
    });

    this.openAllCalls = function () {
        let target = $('.widget-line-overlay__show-all-queues');
        $(filterToggle).addClass('is-checked');
        clearIndicators(target);
        $('[data-toggle-tab]').removeClass('is_active');
        allShow();
        self.show();
    };

    function mergeActiveCalls() {
        let activeCollection = Object.assign({}, queues.active.all());
        let holdCollection = Object.assign({}, queues.hold.all());

        for (let key in holdCollection) {
            if (typeof activeCollection[key] === 'undefined') {
                activeCollection[key] = holdCollection[key];
            } else {
                holdCollection[key].calls.forEach(function (call) {
                    activeCollection[key].calls.push(call);
                });
            }
        }
        return activeCollection;
    }

    function activeShow() {
        ReactDOM.render(
            React.createElement(QueueItem, {groups: mergeActiveCalls(), type: 'active', name: ''}),
            document.getElementById('queue-separator')
        );
        activeQueue = 'active';
    }

    function directShow() {
        ReactDOM.render(
            React.createElement(QueueItem, {groups: queues.direct.all(), type: 'direct', name: ''}),
            document.getElementById('queue-separator')
        );
        activeQueue = 'direct';
    }

    function generalShow() {
        ReactDOM.render(
            React.createElement(QueueItem, {groups: queues.general.all(), type: 'general', name: ''}),
            document.getElementById('queue-separator')
        );
        activeQueue = 'general';
    }

    function allShow() {
        ReactDOM.render(
            React.createElement(AllQueues, {
                active: mergeActiveCalls(),
                direct: queues.direct.all(),
                general: queues.general.all()
            }),
            document.getElementById('queue-separator')
        );
        activeQueue = 'all';
    }

    function isActiveActive() {
        return activeQueue === 'active';
    }

    function isDirectActive() {
        return activeQueue === 'direct';
    }

    function isGeneralActive() {
        return activeQueue === 'general';
    }

    function isAllActive() {
        return activeQueue === 'all';
    }

    this.refresh = function () {
        updateCounters();
        if (isActiveActive()) {
            activeShow();
        } else if (isDirectActive()) {
            directShow();
        } else if (isGeneralActive()) {
            generalShow();
        } else if (isAllActive()) {
            allShow();
        }
     };

    this.show = function () {
        $('.widget-line-overlay').show();
    };

    function updateCounters() {
        $('.call-filter__toggle--line-active').html(queues.hold.count() + queues.active.count());
        $('.call-filter__toggle--line-direct').html(queues.direct.count());
        $('.call-filter__toggle--line-general').html(queues.general.count());
    }

    function clearIndicators (target) {
        var markElement = $('.widget-line-overlay__queue-marker');

        markElement.removeClass('tab-active');
        markElement.removeClass('tab-direct');
        markElement.removeClass('tab-general');
        markElement.removeClass('tab-all');

        switch ($(target).attr('data-call-filter')) {
            case 'active':
                $('[data-queue-marker]').html('Active Calls');
                markElement.addClass('tab-active');
                break;
            case 'direct':
                $('[data-queue-marker]').html('Direct Line');
                markElement.addClass('tab-direct');
                break;
            case 'general':
                $('[data-queue-marker]').html('General Line');
                markElement.addClass('tab-general');
                break;
            case 'all':
                $('[data-queue-marker]').html('Calls Queue');
                break;
        }
    }

    this.hide = function () {
        $('.widget-line-overlay').hide();
        $(filterToggle).removeClass('is-checked');
    }
}
