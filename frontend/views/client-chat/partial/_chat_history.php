<?php
/** @var $history ClientChatMessage[]|null */
/** @var $clientChat ClientChat|null */

use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;

$date = $history[0]->ccm_sent_dt ?? null;
$iDate = null;
$rcUrl = Yii::$app->rchat->host  . '/home';
$userRcAuthToken = Auth::user()->userProfile ? Auth::user()->userProfile->up_rc_auth_token : '';
$readOnly = !$clientChat->isOwner(Auth::id()) || ($clientChat->isArchive() || $clientChat->isClosed()) ? '&readonly=true' : '';
$goto = urlencode('/live/' . $clientChat->cch_rid . '?layout=embedded' . $readOnly);
$randInt = random_int(1, 99999);
?>

<?php if ($clientChat): ?>
    <iframe class="_rc-iframe"
        onload="removeCcLoadFromIframe()"
        src="<?php echo $rcUrl ?>?&layout=embedded<?= $readOnly ?>&resumeToken=<?= $userRcAuthToken ?>&goto=<?= $goto ?>&rnd=<?php echo $randInt ?>"
        id="_rc-<?php echo $clientChat->cch_id ?>"
        style="border: none; width: 100%; height: 100%;"
        name="_<?php echo $randInt ?>"></iframe>
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

