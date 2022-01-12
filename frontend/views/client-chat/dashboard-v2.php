<?php

use frontend\themes\gentelella_v2\assets\ClientChatAsset;
use src\auth\Auth;
use src\helpers\clientChat\ClientChatHelper;
use src\helpers\clientChat\ClientChatIframeHelper;
use src\model\clientChat\dashboard\FilterForm;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\dashboard\ReadUnreadFilter;
use src\model\clientChat\dashboard\GroupFilter;
use src\model\clientChat\permissions\ClientChatActionPermission;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatMessage\entity\ClientChatMessage;
use src\model\clientChatNote\entity\ClientChatNote;
use src\model\userClientChatData\service\UserClientChatDataService;
use src\services\clientChatCouchNote\ClientChatCouchNoteForm;
use yii\bootstrap4\Alert;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ArrayDataProvider|null */
/* @var $client \common\models\Client|null */
/* @var $clientChat \src\model\clientChat\entity\ClientChat|null */
/* @var $history ClientChatMessage|null */
/** @var $totalUnreadMessages int */
/** @var FilterForm $filter */
/** @var ClientChatActionPermission $actionPermissions */
/** @var int $countFreeToTake */
/** @var bool $accessChatError */
/** @var int|null $resetUnreadMessagesChatId */
/** @var ClientChatCouchNoteForm $couchNoteForm */
/** @var array $listParams */

$this->title = 'My Client Chat';

ClientChatAsset::register($this);

$userRcAuthToken = UserClientChatDataService::getCurrentAuthToken() ?? '';

$readonly = (int)ClientChatHelper::isDialogReadOnly($clientChat, Auth::user());
$agentToken = \src\helpers\clientChat\ClientChatDialogHelper::getAgentToken(Auth::user());
$server = Yii::$app->rchat->host;
$apiServer = Yii::$app->rchat->apiServer;
$chatApiScriptUrl = Yii::$app->rchat->chatApiScriptUrl;

$this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.css', [
    'depends' => [ClientChatAsset::class],
]);
$this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.js', ['depends' => [ClientChatAsset::class]])
?>

<?php if ($filter->isEmptyChannels()) : ?>
    <?php echo Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
        'body' => 'You have no assigned channels.',
    ]); ?>
<?php elseif (empty($userRcAuthToken)) : ?>
    <?php echo Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
        'body' => 'You have no assigned token or the token is not valid.',
    ]); ?>
<?php else : ?>
    <div class="row">
        <div class="col-md-3">
            <?php Pjax::begin(['id' => 'pjax-client-chat-channel-list']); ?>
            <div id="_channel_list_wrapper">
                <?= $this->render('partial/_channel_list', [
                    'dataProvider' => $dataProvider,
                    'loadChannelsUrl' => Url::to('/client-chat/dashboard-v2'),
                    'clientChatId' => $clientChat ? $clientChat->cch_id : null,
                    'filter' => $filter,
                    'countFreeToTake' => $countFreeToTake,
                    'resetUnreadMessagesChatId' => $resetUnreadMessagesChatId,
                    'listParams' => $listParams
                ]); ?>
            </div>
            <?php Pjax::end(); ?>
        </div>

        <?php
        $iframeData = null;
        $infoData = null;
        $noteData = null;
        if ($accessChatError) {
            $this->registerJs('createNotify("Client chat view", "You don\'t have access to this chat", "error")', View::POS_LOAD);
        } elseif ($clientChat) {
            $iframeData = (new ClientChatIframeHelper($clientChat))->generateIframe();

            if ($client) {
                $infoData = $this->render(
                    'partial/_client-chat-info',
                    ['clientChat' => $clientChat, 'client' => $client, 'actionPermissions' => $actionPermissions]
                );
            }
            if ($actionPermissions->canNoteView($clientChat) || $actionPermissions->canNoteAdd($clientChat) || $actionPermissions->canNoteDelete($clientChat)) {
                $noteData = $this->render('partial/_client-chat-note', [
                    'clientChat' => $clientChat,
                    'model' => new ClientChatNote(),
                    'actionPermissions' => $actionPermissions,
                ]);
            }
        }
        ?>

        <div class="col-md-6">
            <div id="_rc-iframe-wrapper">
                <?php // $iframeData ?: ''?>
                <?= $this->render('partial/_client_chat_dialog', [
                'agentToken' => $agentToken,
                'server' => $server,
                'rid' => $clientChat->cch_rid ?? null,
                'readonly' => $readonly,
                'apiServer' => $apiServer,
                'chatApiScriptUrl' => $chatApiScriptUrl
            ]) ?>
            </div>
            <?php if ($actionPermissions->canSendCannedResponse()) : ?>
                <?php echo $this->render('partial/_canned_response', ['clientChat' => $clientChat]) ?>
            <?php endif; ?>

            <div id="couch_note_box">
                <?php if ($clientChat && $actionPermissions->canCouchNote($clientChat)) : ?>
                    <?php echo $this->render('partial/_couch_note', ['couchNoteForm' => $couchNoteForm]); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3">
            <div id="_cc_additional_info_wrapper" style="position: relative;">
                <div id="_client-chat-info">
                    <?= $infoData ?: '' ?>
                </div>
                <div id="_client-chat-note">
                    <?= $noteData ?: '' ?>
                </div>
            </div>
        </div>

    </div>
    <?php echo $this->render('partial/bridge_js/_client_chat_common_v2', [
        'clientChat' => $clientChat,
        'filter' => $filter,
        'agentToken' => $agentToken,
        'server' => $server,
        'loadChannelsUrl' => \yii\helpers\Url::to('/client-chat/dashboard-v2'),
        'apiServer' => $apiServer
    ]);
    ?>

<?php endif ?>