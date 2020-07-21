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
/** @var bool $existAvailableLeadQuotes */

$this->title = 'My Client Chat';
$this->params['breadcrumbs'][] = $this->title;

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
                'tab' => $tab
            ]) ?>
        </div>
		<?php Pjax::end() ?>
    </div>
    <div class="col-md-6">
        <div id="_rc-iframe-wrapper" style="height: 100%; width: 100%; position: relative;">
            <?php if ($clientChat && !$clientChat->isClosed()): ?>
                <iframe class="_rc-iframe" src="<?= $rcUrl ?>?layout=embedded&resumeToken=<?= $userRcAuthToken ?>&goto=<?= urlencode('/live/'. $clientChat->cch_rid . '?layout=embedded') ?>" id="_rc-<?= $clientChat->cch_id ?>" style="border: none; width: 100%; height: 100%;" ></iframe>
            <?php elseif ($clientChat && $clientChat->isClosed()): ?>
                <?= $this->render('partial/_chat_history', ['history' => $history, 'clientChat' => $clientChat]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div id="_client-chat-info">
            <?php if ($clientChat): ?>
                <?= $this->render('partial/_client-chat-info', ['clientChat' => $clientChat, 'client' => $client, 'existAvailableLeadQuotes' => $existAvailableLeadQuotes]) ?>
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

<?php
$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$clientChatId = $clientChat ? $clientChat->cch_id : 0;
$js = <<<JS

$(document).ready( function () {
    let clientChatId = {$clientChatId};

    window.name = 'chat';
    if (clientChatId) {
        localStorage.setItem('activeChatId', clientChatId);
    }
    
    $(window).on("beforeunload", function() { 
        localStorage.removeItem('activeChatId');
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

if ($('#_rc-iframe-wrapper').find('._rc-iframe').length) {
    let iframe = $($('#_rc-iframe-wrapper').find('._rc-iframe')[0]);
    let windowHeight = $(window)[0].innerHeight;
    let offsetTop = $("#_rc-iframe-wrapper").offset().top;
    let iframeHeight = windowHeight - offsetTop - 20;
    $(iframe).css('height', iframeHeight+'px');
}

$(document).on('click', '._cc_tab', function () {
    let tab = $(this);
    let params = new URLSearchParams(window.location.search);
    let selectedTab = tab.attr('data-tab-id');
    
    let currentTab = params.get('tab');
    if (currentTab == selectedTab) {
        return false;
    }
    
    params.delete('chid');
    params.delete('channelId');
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
    let windowHeight = $(window)[0].innerHeight;
    let offsetTop = $("#_rc-iframe-wrapper").offset().top;
    let iframeHeight = windowHeight - offsetTop - 20;
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
            iframe.setAttribute('style', 'width: 100%; height: '+iframeHeight+'px; border: none;');
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
            $('#_client-chat-info').append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
        },
        success: function (data) {
            $('#_client-chat-info').html(data.html);
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
        },
    });
    $.ajax({
        type: 'post',
        url: '{$clientChatNoteUrl}',
        dataType: 'json',
        cache: false,
        data: {cch_id: cch_id},
        beforeSend: function () {
            $('#_client-chat-note').append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
        },
        success: function (data) {
            $('#_client-chat-note').html(data.html);
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
        },
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
            dataType: 'html',
            cache: false,
            data: {cchId: cchId},
            beforeSend: function () {
                btn.html('<i class="fa fa-spin fa-spinner"></i>');
            },
            success: function () {
                refreshChatPage(cchId);
                let params = new URLSearchParams(window.location.search);
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

window.getChatHistory = function (cchId) {
    $("#_rc-iframe-wrapper").find('._rc-iframe').hide();
    $("#_rc-iframe-wrapper").find('#_cc-load').remove();
    $("#_rc-iframe-wrapper").append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
    $.post('{$chatHistoryUrl}', {cchId: cchId}, function(data) {
        $("#_rc-iframe-wrapper").append(data);
        $("#_rc-iframe-wrapper").find('#_cc-load').remove();
    });
}

window.refreshChatPage = function (cchId) {
    pjaxReload({container: '#pjax-client-chat-channel-list'});
    $('#_rc-'+cchId).remove();
    $('.cc_transfer').remove();
    $('.cc_close').remove();
    getChatHistory(cchId);
}

$(document).on('click', '.chat-offer', function(e) {
    e.preventDefault();
    let cchId = $(this).attr('data-cch-id');
    let modal = $('#modal-lg');
    
    modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
    modal.find('.modal-title').html('Send Offer');
    modal.modal('show');

    $.ajax({
        type: 'post',
        url: '{$chatSendOfferListUrl}',
        data: {cchId: cchId},
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
     let cchId = $(this).attr('data-cch-id');
     if (!cchId) {
         createNotify('Send Offer', 'Not found Chat Id', 'error');
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
        data: {cchId: cchId, quotesIds: quotes},
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
     let cchId = $(this).attr('data-cch-id');
     if (!cchId) {
         createNotify('Send Offer', 'Not found Chat Id', 'error');
     }
     
     let modal = $('#modal-lg');
     modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');    
    
     $.ajax({
        type: 'post',
        url: '{$chatSendOfferUrl}',
        data: {cchId: cchId},
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
JS;
$this->registerJs($js);
endif;
