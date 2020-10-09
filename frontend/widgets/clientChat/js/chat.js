( function (window, $) {
    var ChatApp = window.ChatApp || {};

    var boxBody = $('#_cc-box-body');
    var accessWg = $('#_cc-access-wg');
    var accessIcon = $('#_cc-access-icon');
    var btnLoadMoreRequests = $('#_cc_load_requests');
    var wrapLoadMoreRequests = $('#_wrap_cc_load_requests');
    var boxHeader = $('._cc-box-header');
    var circleWrapper = $('#_circle_wrapper');

    function Chat(dataLoadUrl, db, page)
    {
        this.dataLoadUrl = dataLoadUrl;
        this.db = db;

        this.countAddedItems = 0;

        this.totalItems = 0;

        this.hasNoRequests = function () {
            accessWg.addClass('inactive');
            boxHeader.removeClass('active');
            circleWrapper.removeClass('active');
            wrapLoadMoreRequests.removeClass('active');
            $('#_client_chat_access_widget ._cc-box-body').html('<p>You have no active client conversations requests.</p>');
            this.disableLoadMoreBtn();
        }

        this.disableLoadMoreBtn = function () {
            btnLoadMoreRequests.html('All request loaded').prop('disabled', true).addClass('disabled');
        }

        this.refreshLoadMoreBtn = function () {
            let itemsCounter = '(' + this.totalItems + ')';
            btnLoadMoreRequests.html('Load More ' + itemsCounter).prop('disabled', false).removeClass('disabled');
        }

        this.firstRequest = function (page) {
            let data = this.loadData(page);

            data.then(() => {this.displayAllRequests(page+1)})
                .then(function () {
                    let _ccWgStatus = localStorage.getItem('_cc_wg_status');
                    let _access = this.db.data.length > 0;
                    if (_ccWgStatus === 'true' && _access) {
                        toggleClientChatAccess();
                    }
                }.bind(this))
                .catch(() => {this.hasNoRequests()})
                .finally(() => {
                    accessIcon.removeClass('fa-spinner fa-spin').addClass('fa-comment-o');
                    accessWg.attr('data-loading', 0);
                });
        }
        this.firstRequest(page);
    }

    Chat.prototype.loadData = function (page)
    {
        return new Promise(function (resolve, reject) {
            let ajax = $.ajax({
                url: this.dataLoadUrl,
                type: 'post',
                dataType: 'json',
                cache: false,
                data: {page: page, countDisplayedRequests: this.db.data.length - this.countAddedItems}
            });

            ajax.done(function (response) {
                if (response.data.length) {
                    this.db.addBatch(response.data)
                        .then(() => {this.db.sortData()})
                        .then(() => {this.totalItems = response.totalItems})
                        .then(() => {resolve()});
                } else {
                    reject();
                }
            }.bind(this))
                .fail(function (xhr) {
                    createNotify('Error', xhr.responseText, 'error');
                });
        }.bind(this));
    }

    Chat.prototype.displayAllRequests = function (nextPage) {
        if (nextPage) {
            btnLoadMoreRequests.attr('data-page', nextPage);
        }
        if (this.db.data.length) {
            boxBody.html('');
            this.db.data.forEach((item) => {boxBody.append(item.html)});
            accessWg.attr('total-items', this.db.data.length).removeClass('inactive');
            $('._cc_total_request_wrapper', accessWg).html(this.db.data.length);
            boxHeader.addClass('active');
            circleWrapper.addClass('active');
            wrapLoadMoreRequests.addClass('active');
            this.refreshLoadMoreBtn();
        }
        if (this.totalItems == this.db.data.length) {
            this.disableLoadMoreBtn();
        }
    }

    Chat.prototype.displayOneRequest = function (request) {
        boxBody.append(request.html);
        accessWg.attr('total-items', this.db.data.length).removeClass('inactive');
        $('._cc_total_request_wrapper', accessWg).html(this.db.data.length);
        boxHeader.addClass('active');
        circleWrapper.addClass('active');
        wrapLoadMoreRequests.addClass('active');
        this.refreshLoadMoreBtn();
        if (this.totalItems == this.db.data.length) {
            this.disableLoadMoreBtn();
        }
    }

    Chat.prototype.removeRequest = function (chatId, userId, chatUserAccessId) {
        this.db.deleteByRequestId(chatUserAccessId)
            .then(() => {
                this.totalItems = parseInt(this.totalItems) - 1;

                $('#ccr_'+chatId+'_'+userId).remove();
                $('._cc_total_request_wrapper', accessWg).html(this.db.data.length);

                if (this.totalItems != this.db.data.length) {
                    this.refreshLoadMoreBtn();
                }
            })
            .then(() => {
                console.log(this.totalItems);
                if (!this.db.data.length && this.totalItems == 0) {
                    this.hasNoRequests();
                } else if (!this.db.data.length && this.totalItems) {
                    this.firstRequest(1);
                }
            });
    }

    Chat.prototype.addRequest = function (request) {
        if (!this.db.data.length) {
            boxBody.html('');
        }
        this.db.add(request)
            .then(() => {this.db.sortData(); this.totalItems = parseInt(this.totalItems) + 1;})
            .then(() => {this.displayOneRequest(request)})
            .then(() => {
                window.enableTimer();
                openWidget();
                this.countAddedItems += 1;
            })
    }

    ChatApp.Chat = Chat;
    window.ChatApp = ChatApp;
})(window, $);