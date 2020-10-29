<?php

/** @var ClientChat|null $clientChat */

use sales\helpers\clientChat\ClientChatIframeHelper;
use sales\model\clientChat\entity\ClientChat;

?>

<?php if ($clientChat): ?>
    <?php echo (new ClientChatIframeHelper($clientChat))->generateIframe(); ?>

<?php
$js = <<<JS
    removeCcLoadFromIframe = function () {
        $('#_rc-iframe-wrapper').find('#_cc-load').remove();
    }
JS;
$this->registerJs($js);
?>

<?php else: ?>
	<?= \yii\bootstrap4\Alert::widget([
        'options' => [
            'class' => 'alert-danger',
            'delay' => 4000

        ],
        'body' => 'Chat is undefined or unable to find chat history',
        'closeButton' => false
    ]) ?>
<?php endif; ?>

