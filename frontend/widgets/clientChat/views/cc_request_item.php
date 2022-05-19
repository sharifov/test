<?php

use common\components\i18n\Formatter;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use src\helpers\clientChat\ClientChatHelper;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Html;

/** @var $access array */
/** @var $formatter Formatter */
/** @var $user Employee */

$accessUrl = \yii\helpers\Url::to('/client-chat/access-manage');
$checkAccessActionUrl = \yii\helpers\Url::to('/client-chat/check-access-action');
$date = $access['cch_updated_dt'];
?>
<div class="_cc-box-item-wrapper" id="ccr_<?= $access['ccua_cch_id'] ?>_<?= $access['ccua_user_id'] ?>" data-is-transfer="<?= (int)$access['is_transfer'] ?>">
    <div class="_cc-box-item">
        <div class="_cc-client-info">
            <span class="_cc-client-name">
                <span class="_cc_access_item_num"></span>
                <i class="fa fa-user"></i>
                <?= Html::encode($access['full_name'] ?: 'Client-' . $access['cch_client_id']) ?>
            </span>

            <div class="_cc-data">
                <?php /*if ($access->ccuaCch->cchDep): ?>
                    <span class="label label-default"><?= Html::encode($access->ccuaCch->cchDep->dep_name) ?></span>
                <?php endif;*/ ?>

                <?php if ($access['project_name']) : ?>
                    <span class="label label-success" style="font-size: 12px"><?= Html::encode($access['project_name']) ?></span>
                <?php endif; ?>

                <span class="label label-default" style="font-size: 12px"><?= Html::encode($access['ccc_name'] ?? 'Not found channel') ?></span>
            </div>

            <div class="col-md-12">
                <span class="_cc-request-created">
                    <small>
                        <?php if ($formatter instanceof Formatter) : ?>
                            <?= $formatter->asByUserDateTime($date) ?>
                        <?php else : ?>
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

            <div>
                <?php if ((int)$access['is_transfer']) : ?>
                    <span class="label label-warning">Transfer</span> from <b><?= Html::encode($access['owner_nickname'] ?? 'Unknown agent') ?></b>
                <?php elseif ((int)$access['is_pending']) : ?>
                    <span class="label label-default">Pending</span>
                <?php elseif ((int)$access['is_idle']) : ?>
                    <span class="label label-info">Idle</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="_cc-action">
            <?php if ((int)$access['is_transfer']) : ?>
                <?= ClientChatHelper::displayBtnAcceptTransfer(
                    $user,
                    $access['ccua_id'],
                    $access['ccua_cch_id'],
                    $accessUrl,
                    ClientChatUserAccess::STATUS_TRANSFER_ACCEPT,
                    $checkAccessActionUrl
                ) ?>
                <?= ClientChatHelper::displayBtnSkipTransfer(
                    $user,
                    $access['ccua_id'],
                    $access['ccua_cch_id'],
                    $accessUrl,
                    ClientChatUserAccess::STATUS_TRANSFER_SKIP,
                    $checkAccessActionUrl
                ) ?>
            <?php elseif ((int)$access['is_pending']) : ?>
                <?= ClientChatHelper::displayBtnAcceptPending(
                    $user,
                    $access['ccua_id'],
                    $access['ccua_cch_id'],
                    $accessUrl,
                    ClientChatUserAccess::STATUS_ACCEPT,
                    $checkAccessActionUrl
                ) ?>

                <?= ClientChatHelper::displayBtnSkipPending(
                    $user,
                    $access['ccua_id'],
                    $access['ccua_cch_id'],
                    $accessUrl,
                    ClientChatUserAccess::STATUS_SKIP,
                    $checkAccessActionUrl
                ) ?>
            <?php elseif ((int)$access['is_idle']) : ?>
                <?= ClientChatHelper::displayBtnTakeIdle(
                    $user,
                    $access,
                    $accessUrl,
                    ClientChatUserAccess::STATUS_TAKE,
                    $checkAccessActionUrl
                ) ?>
            <?php endif; ?>
        </div>
    </div>
    <hr>
</div>