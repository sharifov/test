<?php

/** @var ClientChat|null $clientChat */

use src\auth\Auth;
use src\helpers\clientChat\ClientChatDialogHelper;
use src\helpers\clientChat\ClientChatHelper;
use src\helpers\clientChat\ClientChatIframeHelper;
use src\model\clientChat\entity\ClientChat;

$readonly = (int)ClientChatHelper::isDialogReadOnly($clientChat, Auth::user());
$agentToken = ClientChatDialogHelper::getAgentToken(Auth::user());
$server = Yii::$app->rchat->host;
?>

<?php if ($clientChat) : ?>
    <?= (new ClientChatIframeHelper($clientChat))->setReadOnly(true)->generateIframe(); ?>
    <?php /* $this->render('_client_chat_dialog', [
        'agentToken' => $agentToken,
        'server' => $server,
        'rid' => $clientChat->cch_rid ?? null,
        'readonly' => $readonly
    ]) */ ?>

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

