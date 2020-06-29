function PhoneWidgetPaneQueue(initQueues) {
    let queues = initQueues;
    let self = this;
    let filterToggle = '.call-filter__toggle';
    let activeQueue = null;

    $(document).on('click', filterToggle, function (e) {
        e.preventDefault();
        let type = $(this).attr('data-call-filter');

        switch (type) {
            case 'hold':
                holdShow();
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

    function holdShow() {
        ReactDOM.render(
            React.createElement(QueueItem, {groups: queues.hold.all(), type: 'hold', name: ''}),
            document.getElementById('queue-separator')
        );
        activeQueue = 'hold';
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
            React.createElement(AllQueues, {queues: queues}),
            document.getElementById('queue-separator')
        );
        activeQueue = 'all';
    }

    function isHoldActive() {
        return activeQueue === 'hold';
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
        if (isHoldActive()) {
            holdShow();
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
        $('.call-filter__toggle--line-hold').html(queues.hold.count());
        $('.call-filter__toggle--line-direct').html(queues.direct.count());
        $('.call-filter__toggle--line-general').html(queues.general.count());
    }

    function clearIndicators (target) {
        var markElement = $('.widget-line-overlay__queue-marker');

        markElement.removeClass('tab-hold');
        markElement.removeClass('tab-direct');
        markElement.removeClass('tab-general');
        markElement.removeClass('tab-all');

        switch ($(target).attr('data-call-filter')) {
            case 'hold':
                $('[data-queue-marker]').html('Calls On Hold');
                markElement.addClass('tab-hold');
                break;
            case 'direct':
                $('[data-queue-marker]').html('Direct Calls');
                markElement.addClass('tab-direct');
                break;
            case 'general':
                $('[data-queue-marker]').html('General Lines');
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
