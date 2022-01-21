<?php

use common\models\Client;
use frontend\themes\gentelella_v2\assets\ClientChatAsset;
use src\auth\Auth;
use src\helpers\clientChat\ClientChatHelper;
use src\model\clientChat\dashboard\FilterForm;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\permissions\ClientChatActionPermission;
use src\model\clientChatNote\entity\ClientChatNote;
use src\services\clientChatCouchNote\ClientChatCouchNoteForm;
use yii\bootstrap4\Alert;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var Client|null $client */
/* @var ClientChat|null $clientChat */
/* @var FilterForm $filter */
/* @var string $iframe */
/* @var ClientChatActionPermission $actionPermissions */
/* @var ClientChatCouchNoteForm $couchNoteForm */
/* @var bool $isClosed */

ClientChatAsset::register($this);

$readonly = (int)ClientChatHelper::isDialogReadOnly($clientChat, Auth::user());
$agentToken = \src\helpers\clientChat\ClientChatDialogHelper::getAgentToken(Auth::user());
$server = Yii::$app->rchat->host;
$apiServer = Yii::$app->rchat->apiServer;
$chatApiScriptUrl = Yii::$app->rchat->chatApiScriptUrl;
?>

<?php if (empty($userRcAuthToken)) : ?>
    <?php echo Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
        'body' => 'You have no assigned token or the token is not valid.',
    ]); ?>
<?php else : ?>
    <?php
        $this->title = ' ' . $clientChat->cchClient->full_name . ' - Chat';
        $this->params['breadcrumbs'][] = ['label' => 'My Client Chat', 'url' => ['index', 'chid' => $clientChat->cch_id]];
        $this->params['breadcrumbs'][] = $this->title;
    ?>

    <div class="row">
        <div class="col-md-9">
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
            <?php if (!$isClosed && $actionPermissions->canSendCannedResponse()) : ?>
                <?php echo $this->render('partial/_canned_response', ['clientChat' => $clientChat]) ?>
            <?php endif; ?>

            <div id="couch_note_box">
                <?php if (!$isClosed && $actionPermissions->canCouchNote($clientChat)) : ?>
                    <?php echo $this->render('partial/_couch_note', ['couchNoteForm' => $couchNoteForm]); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-3">
            <div id="_cc_additional_info_wrapper" style="position: relative;">
                <div id="_client-chat-info">
                    <?php if ($client) : ?>
                        <?php echo $this->render('partial/_client-chat-info', [
                            'clientChat' => $clientChat,
                            'client' => $client,
                            'actionPermissions' => $actionPermissions
                        ]); ?>
                    <?php endif; ?>
                </div>
                <div id="_client-chat-note">
                    <?php if ($actionPermissions->canNoteShow($clientChat)) : ?>
                        <?php echo $this->render('partial/_client-chat-note', [
                            'clientChat' => $clientChat,
                            'model' => new ClientChatNote(),
                            'actionPermissions' => $actionPermissions,
                        ]); ?>
                    <?php endif; ?>
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
<?php endif; ?>