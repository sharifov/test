<?php

use frontend\themes\gentelella_v2\assets\ClientChatAsset;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $channels ClientChatChannel[] */
/* @var $dataProvider ActiveDataProvider|null */
/* @var $page int */
/* @var $channelId int|null */

$this->title = 'My Client Chat';
$this->params['breadcrumbs'][] = $this->title;

$loadChannelsUrl = Url::to('/client-chat/index');
ClientChatAsset::register($this);
?>

<div class="row">
    <div class="col-md-3">
        <div id="_channel_list_wrapper">
            <?= $this->render('partial/_channel_list', [
                'channels' => $channels,
                'dataProvider' => $dataProvider,
                'loadChannelsUrl' => $loadChannelsUrl,
                'page' => $page,
                'channelId' => $channelId
            ]) ?>
        </div>
    </div>
    <div class="col-md-6"></div>
    <div class="col-md-3"></div>
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
})
JS;
$this->registerJs($js);





