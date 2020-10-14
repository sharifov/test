<?php
use common\components\i18n\Formatter;
use common\models\ClientEmail;
use common\models\ClientPhone;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Html;

/** @var $access array */
/** @var $formatter Formatter */

$accessUrl = \yii\helpers\Url::to('/client-chat/access-manage');

$date = (int)$access['is_transfer'] ? $access['ccua_created_dt'] : $access['cch_created_dt'];
?>
<div class="_cc-box-item-wrapper" id="ccr_<?= $access['ccua_cch_id'] ?>_<?= $access['ccua_user_id'] ?>" data-is-transfer="<?= (int)$access['is_transfer'] ?>">
	<div class="_cc-box-item">
		<div class="_cc-client-info">
            <span class="_cc-client-name">
                <span class="_cc_access_item_num"></span>
                <i class="fa fa-user"></i>
                <?= Html::encode($access['full_name'] ?: 'Guest-' . $access['cch_client_id']) ?>
            </span>

            <div class="_cc-data">
                <?php /*if ($access->ccuaCch->cchDep): ?>
                    <span class="label label-default"><?= Html::encode($access->ccuaCch->cchDep->dep_name) ?></span>
                <?php endif;*/ ?>

                <?php if ($access['project_name']): ?>
                    <span class="label label-success" style="font-size: 12px"><?= Html::encode($access['project_name']) ?></span>
                <?php endif; ?>

                <span class="label label-default" style="font-size: 12px"><?= Html::encode($access['ccc_name'] ?? 'Not found channel') ?></span>
            </div>

            <div class="col-md-12">
                <span class="_cc-request-created">
                    <small>
                        <?php if ($formatter instanceof Formatter): ?>
                            <?= $formatter->asByUserDateTime($date) ?>
                        <?php else: ?>
                            <?= $formatter->asDatetime($date) ?>
                        <?php endif; ?>
                    </small>
                </span>
                <?php $period = round((time() - strtotime($date))); ?>
                <span title="Relative Time">
                    <i class="fa fa-clock-o"></i>
                    <span data-moment="<?= $period ?>" class="_cc_request_relative_time">
                    </span>
                <?php /*
                    $timeSec = strtotime($date);
                    if ($timeSec >= (60 * 60 * 24)) {
                        echo '<i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asRelativeTime($timeSec);
                    } else {
                        if ($formatter instanceof Formatter) {
                            echo $formatter->asTimer($date);
                        }
                    } */
                ?>
                </span>
            </div>

			<?php if ((int)$access['is_transfer']): ?>
				<div>
					<span class="label label-warning">Transfer</span> from <b><?= Html::encode($access['owner_nickname'] ?? 'Unknown agent') ?></b>
				</div>
			<?php endif; ?>
		</div>

		<div class="_cc-action">
			<button class="btn btn-sm btn-success _cc-access-action" data-ccua-id="<?= $access['ccua_id'] ?>" data-cch-id="<?= $access['ccua_cch_id'] ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_ACCEPT ?>"><i class="fa fa-check"></i> Accept</button>
			<button class="btn btn-sm btn-warning _cc-access-action" data-ccua-id="<?= $access['ccua_id'] ?>" data-cch-id="<?= $access['ccua_cch_id'] ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_SKIP ?>"><i class="fa fa-close"></i> Skip</button>
		</div>

		<!--                            <span class="_cc_chevron"><i class="fa fa-chevron-down"></i></span>-->
	</div>
	<hr>
</div>