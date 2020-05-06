let PhoneWidgetContacts = function () {

    let titleAccessGetMessages = '';
    let disabledClass = '';
    let urlFullList = '';
    let fullList = null;
    let simpleBar = null;
    let currentFullListContainer = true;

    function init(titleAccessGetMessagesInit, disabledClassInit, urlFullListInit) {
        titleAccessGetMessages = titleAccessGetMessagesInit;
        disabledClass = disabledClassInit;
        urlFullList = urlFullListInit;
    }

    function getUrlFullList() {
        return urlFullList;
    }

    function setFullList(list) {
        fullList = list;
    }

    function getFullList() {
        return fullList;
    }

    function addToFullList(list) {
        if (!list) {
            return;
        }
        $.each(list, function (i, item) {
            if (!i || !item) {
                return;
            }
            if (fullList[i]) {
                $.each(list[i], function (j, contact) {
                    fullList[i][fullList[i].length] = contact;
                });
            } else {
                fullList[i] = item;
            }
        });
    }

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    function showPreloader() {
        $($current).append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
    }

    function hidePreloader() {
        $($current).find('.wg-history-load').remove();
    }

    function getContactItem(contact) {
        let content = '<li class="calls-history__item contact-info-card is-collapsible">' +
            '<div class="collapsible-toggler collapsed" data-toggle="collapse" data-target="#collapse' + contact['id'] + '" aria-expanded="false" aria-controls="collapse' + contact['id'] + '">' +
            '<div class="contact-info-card__status">' +
            '<div class="agent-text-avatar">' +
            '<span>' + contact['avatar'] + '</span>' +
            '</div>' +
            '</div>' +
            '<div class="contact-info-card__details">' +
            '<div class="contact-info-card__line history-details">' +
            '<strong class="contact-info-card__name">' + contact['name'] + '</strong>' +
            '</div>' +
            '<div class="contact-info-card__line history-details">' +
            '<span class="contact-info-card__call-type">' + contact['description'] + '</span>' +
            '</div>' +
            '<a href="#" class="collapsible-arrow"><i class="fas fa-chevron-right"></i></a>' +
            '</div>' +
            '</div>' +
            '<div id="collapse' + contact['id'] + '" class="collapse collapsible-container" aria-labelledby="headingOne" data-parent="#contacts-tab">' +
            '<ul class="contact-options-list">' +
            '<li class="contact-options-list__option js-toggle-contact-info" data-contact="' + encode(contact) + '">' +
            '<i class="fa fa-user"></i>' +
            '<span>View</span>' +
            '</li>' +
            '</ul>' +
            '<ul class="contact-full-info">';
        if (contact['phones']) {
            contact['phones'].forEach(function(phone, index) {
                content += getPhoneItem(phone, index, contact);
            })
        }
        if (contact['emails']) {
            contact['emails'].forEach(function(email, index) {
                content += getEmailItem(email, index)
            })
        }
        content += '</ul>' +
            '</div>' +
            '</li>';
        return content;
    }

    function encode(content) {
        return btoa(JSON.stringify(content));
    }

    function decode(content) {
        return JSON.parse(atob(content));
    }

    function viewContact(contact) {
        let content = '<div class="widget-phone__contact-info-modal widget-modal contact-modal-info">' +
            '<a href="#" class="widget-modal__close">' +
            '<i class="fa fa-arrow-left"></i>' +
            'Back to contacts</i>' +
            '</a>' +
            '<div class="contact-modal-info__user">' +
            '<div class="agent-text-avatar">' +
            '<span>' + contact['avatar'] + '</span>' +
            '</div>' +
            '<h3 class="contact-modal-info__name">' + contact['name'] + '</h3>' +
            //                '<div class="contact-modal-info__actions">' +
            //                    '<ul class="contact-options-list">' +
            //                        '<li class="contact-options-list__option js-edit-mode">' +
            //                            '<i class="fa fa-user"></i>' +
            //                            '<span>EDIT</span>' +
            //                        '</li>' +
            //                        '<li class="contact-options-list__option js-trigger-messages-modal">' +
            //                            '<i class="fa fa-comment-alt"></i>' +
            //                            '<span>SMS</span>' +
            //                        '</li>' +
            //                        '<li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">' +
            //                            '<i class="fa fa-phone"></i>' +
            //                            '<span>Call</span>' +
            //                        '</li>' +
            //                    '</ul>' +
            //                '</div>' +
            '</div>' +
            '<div class="contact-modal-info__body">' +
            '<ul class="contact-modal-info__contacts contact-full-info">' +
            '<li>' +
            '<div class="form-group">' +
            '<label for="">Type</label>' +
            '<div class="form-control-wrap" data-type="company">' +
            '<i class="fa fa-building contact-type-company"></i>' +
            '<i class="fa fa-user contact-type-person"></i>' +
            '<select readonly type="text" class="form-control select-contact-type" value="Company" autocomplete="off" readonly disabled>';
        if (contact['is_company']) {
            content += '<option value="company" selected="selected">Company</option> <option value="person">Person</option>';
        } else {
            content += '<option value="company">Company</option> <option value="person"  selected="selected">Person</option>';
        }
        content += '</select>' +
            '</div>' +
            '</div>' +
            '</li>';
        if (contact['phones']) {
            contact['phones'].forEach(function(phone, index) {
                content += getPhoneItem(phone, index, contact);
            })
        }
        if (contact['emails']) {
            contact['emails'].forEach(function(email, index) {
                content += getEmailItem(email, index)
            })
        }

        content += '</ul>' +
            // '<a href="#" class="contact-modal-info__remove-contact">DELETE CONTACT</a>' +
            '</div>' +
            '</div>';
        return content;
    }

    function getEmailItem(email, index) {
        return '<li>' +
            '<div class="form-group">' +
            '<label for="">Email ' + (index + 1) + '</label>' +
            '<input readonly type="email" class="form-control" value="' + email + '" autocomplete="off">' +
            '</div>' +
            // '<ul class="actions-list">' +
            //     '<li class="actions-list__option js-trigger-email-modal">' +
            //         '<i class="fa fa-envelope"></i>' +
            //     '</li>' +
            // '</ul>' +
            '</li>';
    }

    function getPhoneItem(phone, index, contact) {
        let content = '<li>' +
            '<div class="form-group">' +
            '<label for="">Phone ' + (index + 1) + '</label>' +
            '<input readonly type="text" class="form-control" value="' + phone + '" autocomplete="off">' +
            '</div>' +
            '<ul class="actions-list">' +
            '<li class="actions-list__option actions-list__option--phone js-call-tab-trigger">' +
            '<i class="fa fa-phone phone-dial" data-phone="' + phone + '"></i>' +
            '</li>' +
            '<li title="' + titleAccessGetMessages + '" class="actions-list__option js-trigger-messages-modal' + disabledClass + '" data-contact-id="' + contact['id'] + '" data-contact-phone="' + phone + '">' +
            '<i class="fa fa-comment-alt"></i>' +
            '</li>' +
            '</ul>' +
            '</li>';
        return content;
    }

    function noResultsFound() {
        $("#list-of-contacts").html(noResultsTemplate());
    }

    function noResultsTemplate() {
        return '<div style="width:100%;text-align:center;margin-top:20px">No results found</div>';
    }

    function requestFullList() {
        showPreloader();
        $.ajax({
                type: 'post',
                url: getUrlFullList(),
                data: {},
                dataType: 'json',
            })
            .done(function (data) {
                if (data.results.length < 1) {
                    hidePreloader();
                    noResultsFound();
                    return;
                }
                setPageNumber(data.page);
                setFullList(data.results);
                let content = addContactsToListOfContacts(getFullList());
                hidePreloader();
                $("#list-of-contacts").html("").append(content);
                simpleBar.recalculate();
            })
            .fail(function () {
                hidePreloader();
                new PNotify({title: "Search contacts", type: "error", text: 'Server Error. Try again later', hide: true});
            });
    }

    function requestSearchList(form) {
        showPreloader();
        $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serializeArray(),
                dataType: 'json',
            }
        )
            .done(function(data) {
                if (data.results.length < 1) {
                    hidePreloader();
                    noResultsFound();
                    return;
                }
                let content = addContactsToListOfContacts(data.results);
                hidePreloader();
                $("#list-of-contacts").html("").append(content);
            })
            .fail(function () {
                hidePreloader();
                new PNotify({title: "Search contacts", type: "error", text: 'Server Error. Try again later', hide: true});
            });
    }

    function addContactsToListOfContacts(list) {
        let data = '';
        $.each(list, function (i, item) {
            if (!i || !item) {
                return;
            }
            let content = '<span class="section-separator">' + i + '</span>';
            content += '<ul class="phone-widget__list-item calls-history" id="contacts-tab">';
            $.each(list[i], function (j, contact) {
                content += getContactItem(contact);
            });
            content += '</ul>';
            data += content;
        });
        return data;
    }

    function getPageNumber() {
        return $('#tab-contacts').attr('data-page');
    }

    function setPageNumber(page) {
        $('#tab-contacts').attr('data-page', page);
    }

    function initLazyLoadFullList(simpleBarInit) {
        var ajax = false;
        simpleBar = simpleBarInit;

        simpleBar.getScrollElement().addEventListener('scroll', function (e) {
            if (!currentFullListContainer) {
                return;
            }
            if ((e.target.scrollTop + e.target.clientHeight) === e.target.scrollHeight && !ajax) {
                // ajax call get data from server and append to the div
                var page = getPageNumber();
                $.ajax({
                    url: getUrlFullList(),
                    type: 'post',
                    data: {page: page, uid: userId},
                    dataType: 'json',
                    beforeSend: function () {
                        showPreloader();
                        ajax = true;
                    },
                    success: function (data) {
                        setPageNumber(data.page);
                        addToFullList(data.results);
                        let content = addContactsToListOfContacts(getFullList());
                        hidePreloader();
                        $("#list-of-contacts").html("").append(content);
                        simpleBar.recalculate();
                        if (!data.rows) {
                            ajax = false;
                        }
                    },
                    complete: function () {
                        hidePreloader();
                    },
                    error: function (xhr, error) {
                    }
                });
            }
        });
    }

    function fullListIsEmpty() {
        return !fullList;
    }

    function loadFullList() {
        showPreloader();
        if (fullList) {
            let content = addContactsToListOfContacts(fullList);
            hidePreloader();
            $("#list-of-contacts").html("").append(content);
        } else {
            hidePreloader();
            $("#list-of-contacts").html("");
        }
    }

    function setCurrentFullListContainer() {
        return currentFullListContainer = true;
    }

    function setCurrentSearchListContainer() {
        return currentFullListContainer = false;
    }

    return {
        init: init,
        viewContact: viewContact,
        decodeContact: decode,
        delay: delay,
        requestFullList: requestFullList,
        initLazyLoadFullList: initLazyLoadFullList,
        fullListIsEmpty: fullListIsEmpty,
        loadFullList: loadFullList,
        requestSearchList: requestSearchList,
        setCurrentFullListContainer: setCurrentFullListContainer,
        setCurrentSearchListContainer: setCurrentSearchListContainer,
    }

}();

$('#contact-list-ajax').on('beforeSubmit', function (e) {
    e.preventDefault();
    let yiiform = $(this);
    let q = yiiform.find("input[name=q]").val();
    if (q.length < 2) {
         // new PNotify({title: "Search contacts", type: "warning", text: 'Minimum 2 symbols', hide: true});
        PhoneWidgetContacts.setCurrentFullListContainer();
        return false;
    }
    PhoneWidgetContacts.setCurrentSearchListContainer();
    PhoneWidgetContacts.requestSearchList(yiiform);
    return false;
});

$("#contact-list-ajax-q").on('keyup', PhoneWidgetContacts.delay(function() {
    let contactList = $("#contact-list-ajax");
    let q = contactList.find("input[name=q]").val();
    if (q.length < 2) {
        PhoneWidgetContacts.setCurrentFullListContainer();
        PhoneWidgetContacts.loadFullList();
        return false;
    }
    PhoneWidgetContacts.setCurrentSearchListContainer();
    contactList.submit();
}, 300));

$(document).on('click', ".js-toggle-contact-info", function () {
    let contact = PhoneWidgetContacts.decodeContact($(this).data('contact'));
    let data = PhoneWidgetContacts.viewContact(contact);
    $(".widget-phone__contact-info-modal").html(data);
    $(".widget-phone__contact-info-modal").show();
});

$(document).on('click', '.phone-dial', function(e) {
    e.preventDefault();
    let phone = $(this).data('phone');
    $(".widget-phone__contact-info-modal").hide();
    $('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
    $('.phone-widget__tab').removeClass('is_active');
    $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
    $('#tab-phone').addClass('is_active');
    $("#call-pane__dial-number").val(phone);
    $('.suggested-contacts').removeClass('is_active');
});
