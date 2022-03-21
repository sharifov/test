let PhoneWidgetContacts = function () {

    let titleAccessGetMessages = '';
    let disabledClass = '';
    let urlFullList = '';
    let fullList = null;
    let simpleBar = null;
    let currentFullListContainer = true;

    let selectedContacts = [];
   

    function init(titleAccessGetMessagesInit, disabledClassInit, urlFullListInit) {
        titleAccessGetMessages = titleAccessGetMessagesInit;
        disabledClass = disabledClassInit;
        urlFullList = urlFullListInit;

        window.localStorage.setItem('contactSelectableState', 0);
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

    function showCheckbox(contact, index) {
        // handleContactSelection($('[data-selected-contact]'),contact);

        if (window.localStorage.getItem('contactSelectableState') == '1' && contact.phones.length <= 1) {
            return '<div class="select-contact">' +
            '<div class="checkbox">'+
            '<input type="checkbox" name="checkedContact'+ contact.id +'" id="checkedContact'+ contact.id +'" value="'+ contact.phones[0] +'" data-selected-contact="'+ contact.id +'">'+
            '<label for="checkedContact'+ contact.id +'"></label>'+
            '<label for="checkedContact'+ contact.id +'" data-area-label></label>'+
            '</div>' +
            '</div>'
        }

        return '<a class="collapsible-arrow"><i class="fas fa-chevron-right"></i></a>'
    }


    function showCheckboxMultiple(contact, index) {
        handleContactSelection('[data-selected-contact]',contact);
        if (window.localStorage.getItem('contactSelectableState') == '1' && contact.phones.length >= 2) {
            return '<li class="actions-list__option actions-list__option--if-selectable">'+
            '<div class="checkbox">'+
            '<input type="checkbox" name="checkedContact'+ index + 2 +'" id="checkedContact'+ index + 2 +'" value="'+ contact.phones[index] +'" data-selected-contact="'+ contact.id +'">'+
            '<label for="checkedContact'+ index + 2 +'"></label>'+
            '<label for="checkedContact'+ index + 2 +'" data-area-label></label>'+

            '</div>' +
            '</li>'
        } 
        
        return ''
    }

    function cleanSelectedContacts(elem, elemData) {
        for (var i = 0; i < selectedContacts.length; i++) {
            if (selectedContacts[i].id === parseInt($(elem).attr(elemData))) {
                selectedContacts.splice(selectedContacts.indexOf(selectedContacts[i]), 1);
            }
        }
    }

    function handleContactSelection(current, contact) {
        var elemData = 'data-selected-contact';

        $(document).on('change', current, function() {
           
            
            if ($(this).is(':checked')) {
                $('.submit-selected-contacts').slideDown(250);
                var selected = $('['+ elemData +'="'+ $(this).attr(elemData) +'"]');
                $(selected).prop('checked', false);
               
                $(this).prop('checked', true);

                if (contact.hasOwnProperty('id')  && contact['id'] === parseInt($(this).attr(elemData))) {
                    
                    cleanSelectedContacts($(this), elemData)
                    selectedContacts.push(contact);
                }
            }
            else {
                cleanSelectedContacts($(this), elemData)
            }
            if (selectedContacts.length === 0) {
                $('.submit-selected-contacts').slideUp(150);
            }

            $('.selection-amount__selected').html(selectedContacts.length);
            console.log(selectedContacts)
        })
    }

    function getSelectableState(contact) {
        if (window.localStorage.getItem('contactSelectableState') == '1' && contact.phones.length < 2) {
            return ''
        }
        return 'collapse'
    }

    function getContactItem(contact) {
        let content = '<li class="calls-history__item contact-info-card is-collapsible">' +
            '<div class="collapsible-toggler collapsed" data-toggle="'+ getSelectableState(contact) +'" data-target="#collapse' + contact['id'] + '" aria-expanded="false" aria-controls="collapse' + contact['id'] + '">' +
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
            showCheckbox(contact) +
            '</div>' +
            '</div>' +
            '<div id="collapse' + contact['id'] + '" class="collapse collapsible-container" aria-labelledby="headingOne" data-parent="#contacts-tab">' +
            '<ul class="contact-options-list">' +
            '<li class="contact-options-list__option js-toggle-contact-info" data-contact="' + encode(contact) + '">' +
            '<i class="fa fa-user"></i>';

        if (contact.isInternal) {
            content += '<li class="contact-options-list__option dial-to-user contact-dial-to-user" data-contact="' + encode(contact) + '"> <i class="fa fa-phone"> </i></li>';
        }
        content +=  '</ul>' +
            '<ul class="contact-full-info">';

        if (contact['phones']) {
            contact['phones'].forEach(function(phone, index) {
                content += getPhoneItem(phone, index, contact);
            })
        }
        if (contact['emails']) {
            contact['emails'].forEach(function(email, index) {
                content += getEmailItem(email, index, contact);
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
            '<a class="widget-modal__close">' +
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
            // added markup 
            '<span class="section-separator">General info</span>' +

            // '<ul class="contact-modal-info__contacts contact-full-info">' +
            //
            //
            // '<li>'+
            // '<div class="form-group"><label for="">Type</label>'+
            // '<div class="form-control-wrap" data-type="person"><select readonly="" type="text"'+
            // ' class="form-control select-contact-type" autocomplete="off" disabled="">'+
            // '<option value="company">Company</option>'+
            // '<option value="person" selected="selected">Person</option>'+
            // '</select></div>'+
            // '</div>'+
            // '</li>' +
            //
            //
            // '<li>'+
            // '<div class="form-group"><label for="">Date of Birth</label><input readonly="" type="text" class="form-control"'+
            // ' value="24/07/1970" autocomplete="off"></div>'+
            // '</li>' +
            // '</ul>' +
            // '<span class="section-separator">Project - Wowfare</span>' +
            //
            // '<ul class="contact-modal-info__contacts contact-full-info">' +
            //
            // //
            // // '<li>'+
            // // '<div class="form-group"><label for="">Role</label><input readonly="" type="text" class="form-control"'+
            // // ' value="Supervisor" autocomplete="off"></div>'+
            // // '</li>'+
            //
            //
            // '<li>'+
            // '<div class="form-group"><label for="">Phone </label><input readonly="" type="text" class="form-control"'+
            // 'value="+37369271516" autocomplete="off"></div>'+
            // '<ul class="actions-list">'+
            // '<li class="actions-list__option actions-list__option--phone js-call-tab-trigger"><i class="fa fa-phone phone-dial-contacts"'+
            // 'data-phone="+37369271516"></i></li>'+
            // '<li title="" class="actions-list__option js-trigger-messages-modal" data-contact-id="44"'+
            // 'data-contact-phone="+37369271516" data-contact-type="2"><i class="fa fa-comment-alt"></i></li>'+
            // '</ul>'+
            // '</li>' +
            //
            // '<li>'+
            // '<div class="form-group"><label for="">Email </label><input readonly="" type="email" class="form-control"'+
            // 'value="tandroid@gmail.com" autocomplete="off"></div>'+
            // '<ul class="actions-list">'+
            // '<li class="actions-list__option js-trigger-email-modal"'+
            // 'data-contact="eyJncm91cCI6IlQiLCJpZCI6NDQsIm5hbWUiOiJUZXN0IDIiLCJkZXNjcmlwdGlvbiI6IkFuZHJldyB0ZXN0IiwiYXZhdGFyIjoiVCIsImlzX2NvbXBhbnkiOmZhbHNlLCJ0eXBlIjoyLCJwaG9uZXMiOlsiKzM3MzY5MjcxNTE2Il0sImVtYWlscyI6WyJ0YW5kcm9pZEBnbWFpbC5jb20iXX0="'+
            // 'data-contact-email="tandroid@gmail.com"><i class="fa fa-envelope"></i></li>'+
            // '</ul>'+
            // '</li>' +
            // '</ul>'+


            // '<span class="section-separator">Project - Arangrant</span>' +
            // end added markup
            '<ul class="contact-modal-info__contacts contact-full-info">' +
            '<li>' +
            '<div class="form-group">' +
            '<label for="">Type</label>';

            let type = 'person';
            if (contact['is_company']) {
                type = 'company';
            }

            content += '<div class="form-control-wrap" data-type="' + type + '">';
            // if (type === 'person') {
            //     content += '<i class="fa fa-user contact-type-person"></i>';
            // } else {
            //     content += '<i class="fa fa-building contact-type-company"></i>';
            // }

            content += '<select readonly type="text" class="form-control select-contact-type" autocomplete="off" readonly disabled>';
        if (contact['is_company']) {
            content += '<option value="company" selected="selected">Company</option> <option value="person">Person</option>';
        } else {
            content += '<option value="company">Company</option> <option value="person" selected="selected">Person</option>';
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
                content += getEmailItem(email, index, contact)
            })
        }

        content += '</ul>' +
            // '<a href="#" class="contact-modal-info__remove-contact">DELETE CONTACT</a>' +
            '</div>' +
            '</div>';
        return content;
    }

    function getEmailItem(email, index, contact) {
        return '<li class="contact-full-info__email">' +
            '<div class="form-group">' +
            '<label for="">Email ' + (index + 1) + '</label>' +
            '<input readonly type="email" class="form-control" value="' + email + '" autocomplete="off">' +
            '</div>' +
                '<ul class="actions-list">' +
                    '<li class="actions-list__option js-trigger-email-modal" data-contact="' + encode(contact) + '" data-contact-email="' + email + '">' +
                        '<i class="fa fa-envelope"></i>' +
                    '</li>' +
                '</ul>' +
            '</li>';
    }


    function getPhoneItem(phone, index, contact) {
        let content = '<li class="contact-full-info__phone">' +
            '<div class="form-group">' +
            '<label for="">Phone ' + (index + 1) + '</label>' +
            '<input readonly type="text" class="form-control" value="' + phone + '" autocomplete="off">' +
            '</div>' +
            '<ul class="actions-list">' +
            '<li class="actions-list__option actions-list__option--phone js-call-tab-trigger">';

        let dataUserId = contact.isInternal ? contact.id : '';

        content += '<i class="fa fa-phone phone-dial-contacts" data-contact-id="' + contact['id'] + '"  data-user-id="' + dataUserId + '" data-phone="' + (dataUserId ? contact['name'] : phone) + '" data-title="' + contact['name'] + '"></i>';

        content += '</li>' +
            '<li title="' + titleAccessGetMessages + '" class="actions-list__option js-trigger-messages-modal' + disabledClass + '" ' +
                    'data-contact-id="' + contact['id'] + '" data-contact-phone="' + phone + '" data-contact-type="' + contact['type'] + '">' +
            '<i class="fa fa-comment-alt"></i>' +
            '</li>' +
            showCheckboxMultiple(contact, index) +
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
            .fail(function (e) {
                console.log(e);
                hidePreloader();
                createNotifyByObject({title: "Search contacts", type: "error", text: 'Server Error. Try again later', hide: true});
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
                createNotifyByObject({title: "Search contacts", type: "error", text: 'Server Error. Try again later', hide: true});
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
                    data: {page: page},
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
         // createNotifyByObject({title: "Search contacts", type: "warning", text: 'Minimum 2 symbols', hide: true});
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

// $('.js-add-to-conference').on('click', function() {
//     console.log(window.localStorage)

//     $(this).trigger('selection-contacts');
// });
