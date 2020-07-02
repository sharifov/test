<?php

use frontend\themes\gentelella_v2\assets\ClientChatAsset;
use sales\auth\Auth;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\bootstrap4\Alert;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $channels ClientChatChannel[] */
/* @var $dataProvider ActiveDataProvider|null */
/* @var $page int */
/* @var $channelId int|null */
/* @var $client \common\models\Client|null */
/* @var $clientChat \sales\model\clientChat\entity\ClientChat|null */

$this->title = 'My Client Chat';
$this->params['breadcrumbs'][] = $this->title;

$loadChannelsUrl = Url::to('/client-chat/index');
ClientChatAsset::register($this);

$rcUrl = Yii::$app->rchat->host  . '/home';
$userRcAuthToken = Auth::user()->userProfile ? Auth::user()->userProfile->up_rc_auth_token : '';
$clientChatInfoUrl = Url::toRoute('/client-chat/info');
$clintChatDataIUrl = Url::toRoute('/client-chat/ajax-data-info');
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
        <div id="_channel_list_wrapper">
            <?= $this->render('partial/_channel_list', [
                'channels' => $channels,
                'dataProvider' => $dataProvider,
                'loadChannelsUrl' => $loadChannelsUrl,
                'page' => $page,
                'channelId' => $channelId,
                'clientChatRid' => $clientChat ? $clientChat->cch_rid : null
            ]) ?>
        </div>
    </div>
    <div class="col-md-6">
        <div id="_rc-iframe-wrapper" style="height: 100%; width: 100%; position: relative;">
            <?php if ($clientChat): ?>
                <iframe class="_rc-iframe" src="<?= $rcUrl ?>?layout=embedded&resumeToken=<?= $userRcAuthToken ?>&goto=<?= urlencode('/live/'. $clientChat->cch_rid . '?layout=embedded') ?>" id="_rc-<?= $clientChat->cch_rid ?>" style="border: none; width: 100%; height: 100%;" ></iframe>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-3">
        <div id="_client-chat-info">
            <?php if ($clientChat): ?>
                <?= $this->render('partial/_client-chat-info', ['clientChat' => $clientChat, 'client' => $client]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('#btn-load-channels').on('click', function (e) {
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

$(document).on('click', '._cc-list-item', function () {

    if ($(this).hasClass('active')) {
        return false;
    }
    
    let rcUrl = '{$rcUrl}';
    let userRcAuthToken = '{$userRcAuthToken}';
    let gotoParam = encodeURIComponent($(this).attr('data-goto-param'));
    let rid = $(this).attr('data-rid');
    let iframeHref = rcUrl + '?layout=embedded&resumeToken=' + userRcAuthToken + '&goto=' + gotoParam;
    let windowHeight = $(window)[0].innerHeight;
    let offsetTop = $("#_rc-iframe-wrapper").offset().top;
    let iframeHeight = windowHeight - offsetTop - 20;
    let cch_id = $(this).attr('data-cch-id');
    $("#_rc-iframe-wrapper").find('._rc-iframe').hide();
    $('._cc-list-item').removeClass('active');
    $(this).addClass('active');
    
    if (!$('#_rc-'+rid).length) {
        $("#_rc-iframe-wrapper").find('#_cc-load').remove();
        $("#_rc-iframe-wrapper").append('<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
        
        let iframe = document.createElement('iframe');
        iframe.setAttribute('src', iframeHref);
        iframe.setAttribute('style', 'width: 100%; height: '+iframeHeight+'px; border: none;');
        iframe.onload = function () {
            $('#_rc-iframe-wrapper').find('#_cc-load').remove();
        }
        iframe.setAttribute('class', '_rc-iframe');
        iframe.setAttribute('id', '_rc-'+rid);
        $('#_rc-iframe-wrapper').append(iframe);
    }
    
    $('#_rc-'+rid).show();
    let params = new URLSearchParams(window.location.search);
    params.set('rid', rid);
    window.history.replaceState({}, '', '{$loadChannelsUrl}?'+params.toString());
    
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
            createNotify('Error', 'Server Internal Error', 'error');  
        },
    });
});
JS;
$this->registerJs($js);
endif;




