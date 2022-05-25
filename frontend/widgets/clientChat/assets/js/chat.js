( function (window, $) {
    var ChatApp = window.ChatApp || {};

    var boxBody = $('#_cc-box-body');
    var accessWg = $('#_cc-access-wg');
    var accessIcon = $('#_cc-access-icon');
    var btnLoadMoreRequests = $('#_cc_load_requests');
    var wrapLoadMoreRequests = $('#_wrap_cc_load_requests');
    var boxHeader = $('._cc-box-header');
    var circleWrapper = $('#_circle_wrapper');
    var chatStatusSwitchElem = document.querySelector('.chat-status-switch');

    function Chat(dataLoadUrl, db, page, updateChatStatusUrl)
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
            let itemsCounter = '(' + (this.totalItems - this.db.data.length) + ')';
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
                        let interval = 60;
                        setInterval(function () {
                            $('._cc_request_relative_time').each( function (i, elem) {
                                let seconds = +($(elem).attr('data-moment')) + interval;
                                $(elem).attr('data-moment', seconds);
                                $(elem).html(moment.duration(-seconds, 'seconds').humanize(true));
                            });
                        }, interval*1000);
                    }
                }.bind(this))
                .catch(() => {this.hasNoRequests()})
                .finally(() => {
                    accessIcon.removeClass('fa-spinner fa-spin').addClass('fa-comments-o');
                    accessWg.attr('data-loading', 0);
                });
        }
        this.firstRequest(page);

        this.chatSwitcher = new Switchery(chatStatusSwitchElem, {size: 'small'});
        chatStatusSwitchElem.onchange = function () {
            $.ajax({
                url: updateChatStatusUrl,
                type: 'post',
                dataType: 'json',
                data: {chatStatus: chatStatusSwitchElem.checked},
                beforeSend: function () {
                    this.chatSwitcher.disable();
                }.bind(this),
                success: function (res) {
                    if (res.error) {
                        createNotify('Error', res.message, 'error');
                    }
                }.bind(this),
                complete: function () {
                    this.chatSwitcher.enable();
                }.bind(this),
                error: function (xhr) {
                    createNotify('Error', xhr.responseText, 'error');
                    this.setSwitchery(!chatStatusSwitchElem.checked);
                }.bind(this)
            });
        }.bind(this);

        this.widgetEnable = function () {
            this.setSwitchery(true);
        };

        this.widgetDisable = function () {
            this.setSwitchery(false);
        };

        this.setSwitchery = function (checkedBool) {
            if((checkedBool && !this.chatSwitcher.isChecked()) || (!checkedBool && this.chatSwitcher.isChecked())) {
                this.chatSwitcher.setPosition(true);
            }
        };
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
            this.numberItems();
            accessWg.attr('total-items', this.db.data.length).removeClass('inactive');
            $('._cc_total_request_wrapper', accessWg).html(this.totalItems);
            boxHeader.addClass('active');
            circleWrapper.addClass('active');
            wrapLoadMoreRequests.addClass('active');
            this.updateRequestsRelativeTime();
            this.refreshLoadMoreBtn();
        }
        if (this.totalItems == this.db.data.length) {
            this.disableLoadMoreBtn();
        }
    }

    Chat.prototype.numberItems = function () {
        $('._cc-box-item-wrapper', boxBody).each( function (i, elem) {
            $(elem).find('._cc_access_item_num').html('#' + (i+1));
        });
    }

    Chat.prototype.displayOneRequest = function (request) {
        if (this.db.data.length) {
            boxBody.html('');
            this.db.data.forEach((item) => {boxBody.append(item.html)});
            this.numberItems();
            accessWg.attr('total-items', this.db.data.length).removeClass('inactive');
            $('._cc_total_request_wrapper', accessWg).html(this.totalItems);
            boxHeader.addClass('active');
            circleWrapper.addClass('active');
            wrapLoadMoreRequests.addClass('active');
            this.updateRequestsRelativeTime();
            this.refreshLoadMoreBtn();
        }
        if (this.totalItems == this.db.data.length) {
            this.disableLoadMoreBtn();
        }
    }

    Chat.prototype.updateRequestsRelativeTime = function () {
        $('._cc_request_relative_time').each( function (i, elem) {
            $(elem).html(moment.duration(-$(elem).data('moment'), 'seconds').humanize(true));
        });
    }

    Chat.prototype.removeRequest = function (chatId, userId, chatUserAccessId) {
        this.db.deleteByRequestId(chatUserAccessId)
            .then(() => {
                this.totalItems = parseInt(this.totalItems) - 1;

                $('#ccr_'+chatId+'_'+userId).remove();
                $('._cc_total_request_wrapper', accessWg).html(this.totalItems);
                this.numberItems();

                if (this.totalItems != this.db.data.length) {
                    this.refreshLoadMoreBtn();
                }
            })
            .then(() => {
                if (!this.db.data.length && this.totalItems <= 0) {
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
                this.updateRequestsRelativeTime();
                openWidget();
                this.countAddedItems += 1;
            })
    }

    ChatApp.Chat = Chat;
    window.ChatApp = ChatApp;
})(window, $);