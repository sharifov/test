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
$readOnly = (!$clientChat->isOwner(Auth::id() || ($clientChat->isArchive() || $clientChat->isClosed())) ? '&readonly=true' : '');
$goto = urlencode('/live/' . $clientChat->cch_rid . '?layout=embedded' . $readOnly);
$randInt = random_int(1, 99999);
?>

<?php /* if ($history && $clientChat): ?>
<div class="row">
    <div class="col-md-12">
        <div class="_cc_history">
            <p class="text-center _cc_start_conv">Start of conversation</p>

            <?php foreach ($history as $item): ?>
                <?php $iDate = date('Y-m-d', strtotime($item->ccm_sent_dt)) ?>


            <?php if($date != $iDate): ?>
                <div class="_cc_date_wrapper">
                    <hr>
                    <span class="_cc-date"><?= Yii::$app->formatter->asDatetime($iDate, 'php:d M, Y') ?></span>
                </div>
            <?php $date = $iDate; ?>
            <?php endif; ?>

                <?php if ($item->isMessageFromClient()): ?>
                    <div class="_cc_h_message _cc_h_from_client">
                        <span class="square"><i class="fa fa-user"></i></span>
                        <div class="_cc_conversation_data">
                            <div class="d-flex">
                                <span class="name"><?= $item->username ?></span>
                                <span class="date"><?= date('h:i:s A', strtotime($item->ccm_sent_dt)) ?></span>
                            </div>
                            <p class="message"><?= $item->message ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="_cc_h_message _cc_h_from_agent">
                        <div class="_cc_conversation_data">
                            <div class="d-flex">
                                <span class="date"><?= date('h:i:s A', strtotime($item->ccm_sent_dt)) ?></span>
                                <span class="name"><?= $item->username ?></span>
                            </div>
                            <p class="message"><?= $item->message ?></p>
                        </div>
                        <span class="square"><i class="fa fa-user"></i></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </div>
</div>
<?php else: ?>
<?= \yii\bootstrap4\Alert::widget([
        'options' => [
            'class' => 'alert-danger',
            'delay' => 4000

        ],
        'body' => 'Chat is undefined or unable to find chat history',
        'closeButton' => false
    ]) ?>
<?php endif; */ ?>
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

