<?php

/** @var ClientChat|null $clientChat */

use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;

$iDate = null;
$rcUrl = Yii::$app->rchat->host  . '/home';
$userRcAuthToken = Auth::user()->userProfile ? Auth::user()->userProfile->up_rc_auth_token : '';
$readOnly = '&readonly=true';
$randInt = random_int(1, 99999);
$goto = urlencode('/live/' . $clientChat->cch_rid . '?layout=embedded' . $readOnly . '&rnd=' . $randInt);
?>

<?php if ($clientChat): ?>
    <iframe class="_rc-iframe"
        onload="removeCcLoadFromIframe()"
        src="<?php echo $rcUrl ?>?&layout=embedded<?= $readOnly ?>&resumeToken=<?= $userRcAuthToken ?>&rnd=<?php echo $randInt ?>&goto=<?= $goto ?>"
        id="_rc-<?php echo $clientChat->cch_id ?>"
        style="border: none; width: 100%;"
        name="_<?php echo $randInt ?>_<?php echo $clientChat->cch_status_id ?>"></iframe>

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

