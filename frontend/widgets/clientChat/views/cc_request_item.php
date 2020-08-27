<?php
use common\components\i18n\Formatter;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Html;


/** @var $access ClientChatUserAccess */
/** @var $formatter Formatter */

$accessUrl = \yii\helpers\Url::to('/client-chat/access-manage');
?>
<div class="_cc-box-item-wrapper" id="ccr_<?= $access->ccua_cch_id ?>_<?= $access->ccua_user_id ?>">
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
                                            <code><?= Html::encode($email->email) ?></code>
                                        </span>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ($access->ccuaCch->cchClient && $access->ccuaCch->cchClient->clientPhones): ?>
				<?php foreach($access->ccuaCch->cchClient->clientPhones as $phone): ?>
					<span class="_cc-client-phone">
                                            <i class="fa fa-phone"></i>
                                            <code><?= Html::encode($phone->phone) ?></code>
                                        </span>
				<?php endforeach; ?>
			<?php endif; ?>

			<span class="_cc-request-created">
                                    <?php if ($formatter instanceof Formatter): ?>
										<?= $formatter->asByUserDateTime($access->ccua_created_dt) ?>
									<?php else: ?>
										<?= $formatter->asDatetime($access->ccua_created_dt) ?>
									<?php endif; ?>
                                </span>

			<?php if ($formatter instanceof Formatter): ?>
				<span>
                                        <?= $formatter->asTimer($access->ccua_created_dt) ?>
                                    </span>
			<?php endif; ?>
			<div class="_cc-data">
				<?php if ($access->ccuaCch->cchDep): ?>
					<span class="label label-default"><?= Html::encode($access->ccuaCch->cchDep->dep_name) ?></span>
				<?php endif; ?>

				<?php if ($access->ccuaCch->cchProject): ?>
					<span class="label label-default"><?= Html::encode($access->ccuaCch->cchProject->name) ?></span>
				<?php endif; ?>

				<span class="label label-default"><?= Html::encode($access->ccuaCch->cchChannel ? $access->ccuaCch->cchChannel->ccc_name : '') ?></span>
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