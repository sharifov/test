<?php

use frontend\themes\gentelella_v2\assets\ClientChatAsset;
use sales\auth\Auth;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatNote\entity\ClientChatNote;
use yii\bootstrap4\Alert;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $channels ClientChatChannel[] */
/* @var $dataProvider ActiveDataProvider|null */
/* @var $page int */
/* @var $channelId int|null */
/* @var $client \common\models\Client|null */
/* @var $clientChat \sales\model\clientChat\entity\ClientChat|null */
/* @var $history ClientChatMessage|null */
/* @var $tab int */
/* @var $dep int */
/** @var $project int */
/** @var $totalUnreadMessages int */

$this->title = 'My Client Chat';
//$this->params['breadcrumbs'][] = $this->title;

$loadChannelsUrl = Url::to('/client-chat/index');
ClientChatAsset::register($this);

$rcUrl = Yii::$app->rchat->host  . '/home';
$userRcAuthToken = Auth::user()->userProfile ? Auth::user()->userProfile->up_rc_auth_token : '';
$clientChatInfoUrl = Url::toRoute('/client-chat/info');
$clientChatNoteUrl = Url::toRoute('/client-chat/note');
$clintChatDataIUrl = Url::toRoute('/client-chat/ajax-data-info');
$clientChatCloseUrl = Url::toRoute('/client-chat/ajax-close');
$chatHistoryUrl = Url::toRoute('/client-chat/ajax-history');
$chatTransferUrl = Url::toRoute('/client-chat/ajax-transfer-view');
$chatSendOfferListUrl = Url::toRoute('/client-chat/send-offer-list');
$chatSendOfferPreviewUrl = Url::toRoute('/client-chat/send-offer-preview');
$chatSendOfferGenerateUrl = Url::toRoute('/client-chat/send-offer-generate');
$chatSendOfferUrl = Url::toRoute('/client-chat/send-offer');
?>

<?php if (empty($channels)): ?>
    <?php echo Alert::widget([
		'options' => [
			'class' => 'alert-warning',
		],
		'body' => 'You have no assigned channels.'
    ]); ?>
<?php elseif (empty($userRcAuthToken)): ?>
	<?php echo Alert::widget([
		'options' => [
			'class' => 'alert-warning',
		],
		'body' => 'You have no assigned token or the token is not valid.'
	]); ?>
<?php else: ?>

<div class="row">
    <div class="col-md-3">
        <?php Pjax::begin(['id' => 'pjax-client-chat-channel-list'])?>
        <div id="_channel_list_wrapper">
            <?= $this->render('partial/_channel_list', [
                'channels' => $channels,
                'dataProvider' => $dataProvider,
                'loadChannelsUrl' => $loadChannelsUrl,
                'page' => $page,
                'channelId' => $channelId,
                'clientChatId' => $clientChat ? $clientChat->cch_id : null,
                'tab' => $tab,
                'dep' => $dep,
                'project' => $project,
                'totalUnreadMessages' => $totalUnreadMessages,
            ]) ?>
        </div>
		<?php Pjax::end() ?>
    </div>
    <div class="col-md-6">
        <div id="_rc-iframe-wrapper">
            <?php if ($clientChat && !$clientChat->isClosed()): ?>
                <iframe class="_rc-iframe" src="<?= $rcUrl ?>?layout=embedded&resumeToken=<?= $userRcAuthToken ?>&goto=<?= urlencode('/live/'. $clientChat->cch_rid . '?layout=embedded') ?>" id="_rc-<?= $clientChat->cch_id ?>" style="border: none; width: 100%; height: 100%;" ></iframe>
            <?php elseif ($clientChat && $clientChat->isClosed()): ?>
				<?= $this->render('partial/_chat_history', ['clientChat' => $clientChat]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div id="_cc_additional_info_wrapper" style="position: relative;">
            <div id="_client-chat-info">
                <?php if ($clientChat): ?>
                    <?= $this->render('partial/_client-chat-info', ['clientChat' => $clientChat, 'client' => $client]) ?>
                <?php endif; ?>
            </div>

            <div id="_client-chat-note">
                <?php if ($clientChat): ?>
                    <?php echo $this->render('partial/_client-chat-note', [
                        'clientChat' => $clientChat,
                        'model' => new ClientChatNote(),
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$clientChatId = $clientChat ? $clientChat->cch_id : 0;
$discardUnreadMessageUrl = Url::to(['/client-chat/discard-unread-messages']);
$js = <<<JS

window.name = 'chat';
$(document).ready( function () {
    let clientChatId = {$clientChatId};

    if (clientChatId) {
        localStorage.setItem('activeChatId', clientChatId);
    }
    
    $(window).on("beforeunload", function() { 
        localStorage.removeItem('activeChatId');
        window.name = '';
    })
    
    document.addEventListener("visibilitychange", function () {
        if (window.name === 'chat') {
            let activeChatId = $('._cc-list-wrapper').find('._cc-list-item._cc_active').attr('data-cch-id');
            let params = new URLSearchParams(window.location.search);
            let chatId = params.get('chid');
            if (activeChatId == chatId) {
                $.post('{$discardUnreadMessageUrl}', {cchId: activeChatId});
            }
        }
    })
});

$(document).ready( function () {
    $('._cc-item-last-message-time[data-moment]').each( function (i, elem) {
        $(elem).html(moment.duration(-$(elem).data('moment'), 'seconds').humanize(true));
    });
    let interval = 60;
    setInterval(function () {
         $('._cc-item-last-message-time[data-moment]').each( function (i, elem) {
             let seconds = +($(elem).attr('data-moment')) + interval;
             $(elem).attr('data-moment', seconds);
             $(elem).html(moment.duration(-seconds, 'seconds').humanize(true));
         });
    }, interval*1000); 
});

$(document).on('click', '#btn-load-channels', function (e) {
    e.preventDefault();
    
    let page = $(this).attr('data-page');
    let btn = $(this);
    let btnCurrentText = btn.html();
    let selectedChannel = $('#channel-list').val();
    let params = new URLSearchParams(window.location.search);
    let url = '{$loadChannelsUrl}?&page='+page;
    
    if (selectedChannel > 0) {
        url = url+'&channelId='+selectedChannel;
        params.set('channelId', selectedChannel);
    }

    $.ajax({
        type: 'post',
        url: url,
        dataType: 'json',
        cache: false,
        data: {loadingChannels: 1, channelId: params.get('channelId') | selectedChannel},
        beforeSend: function () {
            btn.html('<i class="fa fa-spin fa-spinner"></i> Loading...').prop('disabled', true).addClass('disabled');
        },
        success: function (data) {
            if (data.html) {
                $('._cc-list-wrapper').append(data.html);
                btn.html(btnCurrentText).removeAttr('disabled').removeClass('disabled').attr('data-page', data.page);
            } else {
                btn.html('All conversations are loaded');
            }
            params.set('page', data.page);
            window.history.replaceState({}, '', '{$loadChannelsUrl}?'+params.toString());
        },
        error: function (xhr) {
            btn.html(btnCurrentText);
        },
    });
});

// if ($('#_rc-iframe-wrapper').find('._rc-iframe').length) {
//     let iframe = $($('#_rc-iframe-wrapper').find('._rc-iframe')[0]);
//     let windowHeight = $(window)[0].innerHeight;
//     let offsetTop = $("#_rc-iframe-wrapper").offset().top;
//     let iframeHeight = windowHeight - offsetTop - 20;
//     $(iframe).css('height', iframeHeight+'px');
// }

$(document).on('click', '._cc_tab', function () {
    let tab = $(this);
    let params = new URLSearchParams(window.location.search);
    let selectedTab = tab.attr('data-tab-id');
    
    let currentTab = params.get('tab');
    if (currentTab == selectedTab) {
        return false;
    }
    
    params.delete('chid');
    params.delete('page');
    params.set('tab', selectedTab);
    window.history.replaceState({}, '', '{$loadChannelsUrl}?'+params.toString());
    $('._cc_tab').removeClass('active');
    tab.addClass('active');
    $('._rc-iframe').hide();
    $('#_client-chat-info').html('');
    $('#_client-chat-note').html('');
    pjaxReload({container: '#pjax-client-chat-channel-list'});
});

$(document).on('click', '._cc-list-item', function () {

    if ($(this).hasClass('_cc_active')) {
        return false;
    }
    
    let rcUrl = '{$rcUrl}';
    let userRcAuthToken = '{$userRcAuthToken}';
    let gotoParam = encodeURIComponent($(this).attr('data-goto-param'));
    let iframeHref = rcUrl + '?layout=embedded&resumeToken=' + userRcAuthToken + '&goto=' + gotoParam;
    // let windowHeight = $(window)[0].innerHeight;
    // let offsetTop = $("#_rc-iframe-wrapper").offset().top;
    // let iframeHeight = windowHeight - offsetTop - 20;
    let cch_id = $(this).attr('data-cch-id');
    let isClosed = $(this).attr('data-is-closed');
    $("#_rc-iframe-wrapper").find('._rc-iframe').hide();
    $('._cc-list-item').removeClass('_cc_active');
    $(this).addClass('_cc_active');
    
    if (!$('#_rc-'+cch_id).length) {
    
        if (isClosed) {
            getChatHistory(cch_id);
        } else {
            $("#_rc-iframe-wrapper").find('#_cc-load').remove();
            $("#_rc-iframe-wrapper").append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
            
            let iframe = document.createElement('iframe');
            iframe.setAttribute('src', iframeHref);
            // iframe.setAttribute('style', 'width: 100%; height: '+iframeHeight+'px; border: none;');
            iframe.onload = function () {
                $('#_rc-iframe-wrapper').find('#_cc-load').remove();
            }
            iframe.setAttribute('class', '_rc-iframe');
            iframe.setAttribute('id', '_rc-'+cch_id);
            $('#_rc-iframe-wrapper').append(iframe);
        }
    }
    
    let params = new URLSearchParams(window.location.search);
    params.set('chid', cch_id);
    window.history.replaceState({}, '', '{$loadChannelsUrl}?'+params.toString());
    
    localStorage.setItem('activeChatId', cch_id);
    
    $('#_rc-'+cch_id).show();
    $.ajax({
        type: 'post',
        url: '{$clientChatInfoUrl}',
        dataType: 'json',
        cache: false,
        data: {cch_id: cch_id},
        beforeSend: function () {
            $('#_cc_additional_info_wrapper').append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
        },
        success: function (data) {
            $('#_client-chat-info').html(data.html);
            $('#_client-chat-note').html(data.noteHtml);
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
        },
        complete: function () {
            $('#_cc_additional_info_wrapper #_cc-load').remove();
        }
    });
});

$(document).on('click', '.cc_full_info', function (e) {
    e.preventDefault();
    let cchId = $(this).attr('data-cch-id');
    let modal = $('#modal-lg');
    
    $.ajax({
        type: 'post',
        url: '{$clintChatDataIUrl}',
        dataType: 'html',
        cache: false,
        data: {cchId: cchId},
        beforeSend: function () {
            modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
            modal.find('.modal-title').html('Client Chat Info');
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
        },
    });
});

$(document).on('click', '.cc_transfer', function (e) {
    e.preventDefault();
    let cchId = $(this).attr('data-cch-id');
    let modal = $('#modal-sm');
    
    $.ajax({
        type: 'post',
        url: '{$chatTransferUrl}',
        dataType: 'html',
        cache: false,
        data: {cchId: cchId},
        beforeSend: function () {
            modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
            modal.find('.modal-title').html('Client Chat Transfer');
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
        },
        error: function (xhr) {
            modal.modal('hide');
            createNotify('Error', xhr.responseText, 'error');
        },
    });
});

$(document).on('click', '.cc_close', function (e) {
    e.preventDefault();
    let btn = $(this);
    let cchId = btn.attr('data-cch-id');
    let btnHtml = btn.html();
    
    if (confirm('Confirm close chat')) {
        $.ajax({
            type: 'post',
            url: '{$clientChatCloseUrl}',
            dataType: 'json',
            cache: false,
            data: {cchId: cchId},
            beforeSend: function () {
                btn.html('<i class="fa fa-spin fa-spinner"></i>');
            },
            success: function (data) {
                if (data.error) {
                    createNotify('Error', data.message, 'error');
                } else {
                    refreshChatPage(cchId, data.tab);
                    createNotify('Success', data.message, 'success');
                }
            },
            complete: function () {
                btn.html(btnHtml);
            },
            error: function (xhr) {
                createNotify('Error', xhr.responseText, 'error');
            }
        })
    }
});

window.removeCcLoadFromIframe = function () {
    $('#_rc-iframe-wrapper').find('#_cc-load').remove();
}

window.getChatHistory = function (cchId) {
    $("#_rc-iframe-wrapper").find('._rc-iframe').hide();
    $("#_rc-iframe-wrapper").find('#_cc-load').remove();
    $("#_rc-iframe-wrapper").append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
    $.post('{$chatHistoryUrl}', {cchId: cchId}, function(data) {
        $("#_rc-iframe-wrapper").append(data);
    });
}

window.refreshChatPage = function (cchId, tab) {
    if (tab) {
        let params = new URLSearchParams(window.location.search);
        params.set('tab', tab);
        window.history.replaceState({}, '', '{$loadChannelsUrl}?'+params.toString());
    }
    pjaxReload({container: '#pjax-client-chat-channel-list'});
    $('#_rc-'+cchId).remove();
    $('.cc_transfer').remove();
    $('.cc_close').remove();
    getChatHistory(cchId);
}

$(document).on('click', '.chat-offer', function(e) {
    e.preventDefault();
    let chatId = $(this).attr('data-chat-id');
    let leadId = $(this).attr('data-lead-id');
    let modal = $('#modal-lg');
    
    modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
    modal.find('.modal-title').html('Send Offer');
    modal.modal('show');

    $.ajax({
        type: 'post',
        url: '{$chatSendOfferListUrl}',
        data: {chat_id: chatId, lead_id: leadId},
        dataType: 'html'
    })
    .done(function(data) { 
            modal.find('.modal-body').html(data);
    })
    .fail(function () {
            createNotify('Error', 'Server error', 'error');
    });
});

$(document).on('click', '.quotes-uid-chat-generate', function(e) {
    e.preventDefault();
     let chatId = $(this).attr('data-chat-id');
     let leadId = $(this).attr('data-lead-id');
     if (!chatId) {
         createNotify('Send Offer', 'Not found Chat Id', 'error');
         return;
     }
     if (!leadId) {
         createNotify('Send Offer', 'Not found Lead Id', 'error');
         return;
     }
    
    let quotes = [];
       
    $('input[type=checkbox].quotes-uid:checked').each(function() {
        quotes.push($(this).data('id'));
    });
    
    if (quotes.length < 1) {
        createNotify('Send Offer', 'Not found selected quotes', 'error');
        return false;
    }
    
    let modal = $('#modal-lg');
    modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');    
    
     $.ajax({
        type: 'post',
        url: '{$chatSendOfferGenerateUrl}',
        data: {chatId: chatId, leadId: leadId, quotesIds: quotes},
        dataType: 'html'
    })
    .done(function(data) { 
            modal.find('.modal-body').html(data);
    })
    .fail(function () {
            createNotify('Error', 'Server error', 'error');
    });    
        
});

$(document).on('click', '.client-chat-send-offer', function(e) {
    e.preventDefault();
     let chatId = $(this).attr('data-chat-id');
     let leadId = $(this).attr('data-lead-id');
     if (!chatId) {
         createNotify('Send Offer', 'Not found Chat Id', 'error');
         return;
     }
     if (!leadId) {
         createNotify('Send Offer', 'Not found Lead Id', 'error');
         return;
     }
     
     let modal = $('#modal-lg');
     modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');    
    
     $.ajax({
        type: 'post',
        url: '{$chatSendOfferUrl}',
        data: {chatId: chatId, leadId: leadId},
        dataType: 'json'
    })
    .done(function(data) {
        if (data.error) {
            modal.find('.modal-body').html(data.message);
            return false;
        }
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('');
        modal.modal('hide');
    })
    .fail(function () {
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('');
        modal.modal('hide');
        createNotify('Error', 'Server error', 'error');
    }); 
});    
        
    $("#pjax-notes").on("pjax:start", function () {         
        $("#btn-submit-note").prop("disabled", true).addClass("disabled");
        $("#btn-submit-note i").attr("class", "fa fa-cog fa-spin fa-fw");
    });
    
    $("#pjax-notes").on("pjax:end", function () {           
        $("#btn-submit-note").prop("disabled", false).removeClass("disabled");
        $("#btn-submit-note i").attr("class", "fa fa-plus");        
    });

$(document).on('click', '.create_lead', function (e) {
    e.preventDefault();
    let url = $(this).attr('data-link');
    let modal = $('#modal-md');
    let modalTitle = 'Create Lead';
    $.ajax({
        type: 'post',
        url: url,
        data: {},
        dataType: 'html',
        beforeSend: function () {
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.find('.modal-title').html(modalTitle);
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
            modal.find('.modal-title').html(modalTitle);
            $('#preloader').addClass('d-none');
        },
        error: function (xhr) {
            if (xhr.status === 403) {
                createNotify('Error', xhr.responseText, 'error');
            } else {
                createNotify('Error', 'Internal Server Error. Try again letter.', 'error');
            }
            setTimeout(function () {
                $('#modal-md').modal('hide');
            }, 300)
        },
    })

});
$(document).on('click', '.create_case', function (e) {
    e.preventDefault();
    let url = $(this).attr('data-link');
    let modal = $('#modal-md');
    let modalTitle = 'Create Case';
    $.ajax({
        type: 'post',
        url: url,
        data: {},
        dataType: 'html',
        beforeSend: function () {
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.find('.modal-title').html(modalTitle);
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
            modal.find('.modal-title').html(modalTitle);
            $('#preloader').addClass('d-none');
        },
        error: function (xhr) {
            if (xhr.status === 403) {
                createNotify('Error', xhr.responseText, 'error');
            } else {
                createNotify('Error', 'Internal Server Error. Try again letter.', 'error');
            }
            setTimeout(function () {
                $('#modal-md').modal('hide');
            }, 300)
        },
    })

});

$(document).on('click', '.btn_toggle_form', function (e) {
    $("#clientchatnote-ccn_note").val('');
    let modal = $('#add-note-model');
    modal.modal('show');
});

$(document).on('click', '#btn-submit-note', function (e) {    
    if ($("#clientchatnote-ccn_note").val() !== ''){
       let modal = $('#add-note-model');
        modal.modal('hide');
    }    
});

JS;
$this->registerJs($js);

$css = <<<CSS
    .panel_toolbox .btn {
        border-radius: 100%;
        width: 25px;
        height: 25px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #pjax-notes .x_panel {
        margin-top: 10px;
    }    
CSS;
$this->registerCss($css);

endif;
