<?php

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\ArrayHelper;

/** @var $channels ClientChatChannel[] */
/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ActiveDataProvider|null */
/** @var $loadChannelsUrl string */
/** @var $page int */
/** @var $channelId int|null */
/** @var $clientChatId int|null */
/** @var $tab int */

?>


<div class="_cc-wrapper">
    <div class="_cc_tabs_wrapper">
        <?php foreach (ClientChat::getTabList() as $key => $item): ?>
            <div class="_cc_tab <?= $key === $tab ? 'active' : '' ?>" data-tab-id="<?= $key ?>"> <?= $item ?>
                <?php if (ClientChat::isTabActive($key)): ?>
                    <sup class="_cc_unread_messages"></sup>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
	<div class="_cc-channel-select">
		<?= \yii\helpers\Html::label('Channel list:', null, ['class' => 'control-label']) ?>
		<?= \kartik\select2\Select2::widget([
			'data' => ArrayHelper::merge( ['All'], ArrayHelper::map(ArrayHelper::toArray($channels),'ccc_id', 'ccc_name')),
			'name' => 'channel-list',
			'size' => \kartik\select2\Select2::SIZE_SMALL,
			'pluginEvents' => [
				'change' => new \yii\web\JsExpression('function (e) {
                    let selectedChannel = $(this).val();
                    let btn = $("#btn-load-channels");
                    let params = new URLSearchParams(window.location.search);
                    let url = "'.$loadChannelsUrl.'";
                    let tab = params.get("tab") | '.$tab.';
                    
                    url = url + "?tab="+tab;
                    if (selectedChannel > 0) {
                        url = url + "&channelId="+selectedChannel;
                    }

				    $.ajax({
                        type: "post",
                        url: url,
                        dataType: "json",
                        cache: false,
                        data: {page: 1, channelId: params.get("channelId") | selectedChannel},
                        beforeSend: function () {
                            $("#_channel_list_wrapper").append(\'<div id="_cc-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>\');
                        },
                        success: function (data) {
                            $("._cc-list-wrapper").html(data.html);
                            if (data.html) {
                                btn.html("Load more").removeAttr("disabled").removeClass("disabled").attr("data-page", data.page);
                            } else {
                                btn.html("All conversations are loaded").prop(\'disabled\', true).addClass(\'disabled\');
                            }
                            params.set(\'page\', 1);
                            params.set(\'channelId\', selectedChannel);
                            window.history.replaceState({}, \'\', "'.$loadChannelsUrl.'?"+params.toString());
                        },
                        complete: function () {
                            $("#_channel_list_wrapper").find(\'#_cc-load\').remove();
                        }
                    });
                }')
			],
			'options' => [
				'placeholder' => 'Choose the channel...',
                'id' => 'channel-list'
			],
			'value' => $channelId ?: 0
		]) ?>
	</div>

	<div class="_cc-list-wrapper">
        <?php if ($dataProvider): ?>
		    <?= $this->render('_client-chat-item', ['clientChats' => $dataProvider->getModels(), 'clientChatId' => $clientChatId]) ?>
        <?php endif; ?>
	</div>

	<div class="_cc-channel-pagination" style="display: flex;justify-content: center; padding: 15px 0 10px;">
		<button class="btn btn-default" id="btn-load-channels" data-page="<?= $page ?>">Load more</button>
	</div>
</div>
