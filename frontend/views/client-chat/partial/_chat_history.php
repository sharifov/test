<?php

/** @var ClientChat|null $clientChat */

use sales\auth\Auth;
use sales\helpers\clientChat\ClientChatDialogHelper;
use sales\helpers\clientChat\ClientChatHelper;
use sales\helpers\clientChat\ClientChatIframeHelper;
use sales\model\clientChat\entity\ClientChat;

$readonly = (int)ClientChatHelper::isDialogReadOnly($clientChat, Auth::user());
$agentToken = ClientChatDialogHelper::getAgentToken(Auth::user());
$server = Yii::$app->rchat->host;
?>

<?php if ($clientChat) : ?>
    <?php // (new ClientChatIframeHelper($clientChat))->setReadOnly(true)->generateIframe(); ?>
    <?= $this->render('_client_chat_dialog', [
        'agentToken' => $agentToken,
        'server' => $server,
        'rid' => $clientChat->cch_rid ?? null,
        'readonly' => $readonly
    ]) ?>

    <?php
    $js = <<<JS
    removeCcLoadFromIframe = function () {
        $('#_rc-iframe-wrapper').find('#_cc-load').remove();
    }
JS;
    $this->registerJs($js);
    ?>

<?php else : ?>
    <?= \yii\bootstrap4\Alert::widget([
        'options' => [
            'class' => 'alert-danger',
            'delay' => 4000

        ],
        'body' => 'Chat is undefined or unable to find chat history',
        'closeButton' => false
    ]) ?>
<?php endif;

