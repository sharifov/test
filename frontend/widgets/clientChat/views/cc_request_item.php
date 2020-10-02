<?php
use common\components\i18n\Formatter;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Html;


/** @var $access ClientChatUserAccess */
/** @var $formatter Formatter */

$accessUrl = \yii\helpers\Url::to('/client-chat/access-manage');
?>
<div class="_cc-box-item-wrapper" id="ccr_<?= $access->ccua_cch_id ?>_<?= $access->ccua_user_id ?>" data-is-transfer="<?= (int)$access->ccuaCch->isTransfer() ?>">
	<div class="_cc-box-item">
		<div class="_cc-client-info">
                                <span class="_cc-client-name">
                                    <i class="fa fa-user"></i>
                                    <?= Html::encode($access->ccuaCch->cchClient && $access->ccuaCch->cchClient->full_name ? $access->ccuaCch->cchClient->full_name : 'Guest-' . $access->ccuaCch->cch_id) ?>
                                </span>

			<?php if ($access->ccuaCch->cchClient && $access->ccuaCch->cchClient->clientEmails): ?>
				<?php foreach($access->ccuaCch->cchClient->clientEmails as $email): ?>
					<span class="_cc-client-email">
                                            <i class="fa fa-envelope"></i>
                                            <i><?= Html::encode($email->email) ?></i>
                                        </span>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ($access->ccuaCch->cchClient && $access->ccuaCch->cchClient->clientPhones): ?>
				<?php foreach($access->ccuaCch->cchClient->clientPhones as $phone): ?>
					<span class="_cc-client-phone">
                                            <i class="fa fa-phone"></i>
                                            <i><?= Html::encode($phone->phone) ?></i>
                                        </span>
				<?php endforeach; ?>
			<?php endif; ?>

            <div class="_cc-data">
                <?php /*if ($access->ccuaCch->cchDep): ?>
					<span class="label label-default"><?= Html::encode($access->ccuaCch->cchDep->dep_name) ?></span>
				<?php endif;*/ ?>

                <?php if ($access->ccuaCch->cchProject): ?>
                    <span class="label label-success" style="font-size: 12px"><?= Html::encode($access->ccuaCch->cchProject->name) ?></span>
                <?php endif; ?>

                <span class="label label-default" style="font-size: 12px"><?= Html::encode($access->ccuaCch->cchChannel ? $access->ccuaCch->cchChannel->ccc_name : '') ?></span>
            </div>

            <div class="col-md-12">

                    <span class="_cc-request-created">
                        <small>
                                        <?php if ($formatter instanceof Formatter): ?>
                                            <?= $formatter->asByUserDateTime($access->getTimeByChatStatus()) ?>
                                        <?php else: ?>
                                            <?= $formatter->asDatetime($access->getTimeByChatStatus()) ?>
                                        <?php endif; ?>
                        </small>
                    </span>
                    <span title="Relative Time">
                    <?php
                        $timeSec = strtotime($access->getTimeByChatStatus());
                        if ($timeSec < (60 * 60 * 24) ) {
                            echo '<i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asRelativeTime($timeSec);
                        } else {
                            if ($formatter instanceof Formatter) {
                                echo $formatter->asTimer($access->getTimeByChatStatus());
                            }
                        }
                    ?>
                    </span>


            </div>


			<?php if ($access->ccuaCch->cchOwnerUser && $access->ccuaCch->isTransfer()): ?>
				<div>
					<span class="label label-warning">Transfer</span> from <b><?= Html::encode($access->ccuaCch->cchOwnerUser->nickname) ?></b>
				</div>
			<?php endif; ?>
		</div>


		<div class="_cc-action">
			<button class="btn btn-sm btn-success _cc-access-action" data-ccua-id="<?= $access->ccua_id ?>" data-cch-id="<?= $access->ccua_cch_id ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_ACCEPT ?>"><i class="fa fa-check"></i> Accept</button>
			<button class="btn btn-sm btn-warning _cc-access-action" data-ccua-id="<?= $access->ccua_id ?>" data-cch-id="<?= $access->ccua_cch_id ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_SKIP ?>"><i class="fa fa-close"></i> Skip</button>
		</div>

		<!--                            <span class="_cc_chevron"><i class="fa fa-chevron-down"></i></span>-->
	</div>
	<hr>
</div>