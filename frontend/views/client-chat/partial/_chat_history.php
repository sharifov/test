<?php
/** @var $history ClientChatMessage[]|null */
/** @var $clientChat ClientChat|null */

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;

$date = $history[0]->ccm_sent_dt ?? null;
$iDate = null;
?>

<div class="_rc-iframe" id="_rc-<?= $clientChat->cch_id ?>">
<?php if ($history && $clientChat): ?>
<div class="row">
	<div class="col-md-12">
        <div class="_cc_history">
            <p class="text-center _cc_start_conv">Start of conversation</p>

            <?php foreach ($history as $item): ?>
                <?php $iDate = date('Y-m-d', strtotime($item->ccm_sent_dt)) ?>


            <?php if($date != $iDate): ?>
                <div class="_cc_date_wrapper">
                    <hr>
                    <span class="_cc-date">20 Jule, 2020</span>
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
<?php endif; ?>
</div>

