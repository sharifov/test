<?php

use common\components\i18n\Formatter;
use frontend\widgets\clientChat\ClientChatAsset;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $access ClientChatUserAccess[] */
/** @var $open bool */
/** @var $formatter Formatter */

ClientChatAsset::register($this);

$accessUrl = \yii\helpers\Url::to('/client-chat/access-manage');
$totalRequest = count($access);
?>


<?php // yii\widgets\Pjax::begin(['id' => 'client-chat-box-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => []])?>
<div class="_cc-fabs">
    <div class="_cc-box <?= $open && $access ? 'is-visible' : '' ?>">
        <div class="_cc-box-header <?= $access ? 'active' : '' ?>">
            <div class="_cc-box-option">
                <div class="header_img">
					<?= Html::img('/img/user.png')?>
                </div>
                <span id="_cc-box-title">Client Chat Request</span> <br>
            </div>
        </div>
        <div class="_cc-box-body">
            <?php if($access): ?>
                <?php foreach($access as $item): ?>
                    <div class="_cc-box-item-wrapper" id="ccr_<?= $item->ccua_cch_id ?>_<?= $item->ccua_user_id ?>">
                        <div class="_cc-box-item">
                            <div class="_cc-client-info">
                                <span class="_cc-client-name">
                                    <i class="fa fa-user"></i>
                                    <?= Html::encode($item->ccuaCch->cchClient && $item->ccuaCch->cchClient->full_name ? $item->ccuaCch->cchClient->full_name : 'Guest-' . $item->ccuaCch->cch_id) ?>
                                </span>

                                <?php if ($item->ccuaCch->cchClient && $item->ccuaCch->cchClient->clientEmails): ?>
                                    <?php foreach($item->ccuaCch->cchClient->clientEmails as $email): ?>
                                        <span class="_cc-client-email">
                                            <i class="fa fa-envelope"></i>
                                            <code><?= Html::encode($email->email) ?></code>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if ($item->ccuaCch->cchClient && $item->ccuaCch->cchClient->clientPhones): ?>
                                    <?php foreach($item->ccuaCch->cchClient->clientPhones as $phone): ?>
                                        <span class="_cc-client-phone">
                                            <i class="fa fa-phone"></i>
                                            <code><?= Html::encode($phone->phone) ?></code>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <span class="_cc-request-created">
                                    <?php if ($formatter instanceof Formatter): ?>
                                        <?= Html::encode($formatter->asByUserDateTime($item->ccua_created_dt)) ?>
                                    <?php else: ?>
										<?= Html::encode($formatter->asDatetime($item->ccua_created_dt)) ?>
									<?php endif; ?>
                                </span>

                                    <?php if ($formatter instanceof Formatter): ?>
                                    <span>
                                        <?= Html::encode($formatter->asTimer($item->ccua_created_dt)) ?>
                                    </span>
                                    <?php endif; ?>
                                <div class="_cc-data">
                                    <?php if ($item->ccuaCch->cchDep): ?>
                                        <span class="label label-default"><?= Html::encode($item->ccuaCch->cchDep->dep_name) ?></span>
                                    <?php endif; ?>

                                    <?php if ($item->ccuaCch->cchProject): ?>
                                        <span class="label label-default"><?= Html::encode($item->ccuaCch->cchProject->name) ?></span>
                                    <?php endif; ?>

                                    <span class="label label-default"><?= Html::encode($item->ccuaCch->cchChannel ? $item->ccuaCch->cchChannel->ccc_name : '') ?></span>
                                </div>

                                <?php if ($item->ccuaCch->cchOwnerUser && $item->ccuaCch->isTransfer()): ?>
                                    <div>
                                        <span class="label label-warning">Transfer</span> from <b><?= Html::encode($item->ccuaCch->cchOwnerUser->nickname) ?></b>
                                    </div>
                                <?php endif; ?>
                            </div>


                            <div class="_cc-action">
                                <button class="btn btn-sm btn-success _cc-access-action" data-ccua-id="<?= $item->ccua_id ?>" data-cch-id="<?= $item->ccua_cch_id ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_ACCEPT ?>"><i class="fa fa-check"></i> Accept</button>
                                <button class="btn btn-sm btn-warning _cc-access-action" data-ccua-id="<?= $item->ccua_id ?>" data-cch-id="<?= $item->ccua_cch_id ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_SKIP ?>"><i class="fa fa-close"></i> Skip</button>
                            </div>

<!--                            <span class="_cc_chevron"><i class="fa fa-chevron-down"></i></span>-->
                        </div>
                        <hr>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no active client conversations requests.</p>
            <?php endif; ?>
        </div>

<!--        <div class="fab_field">-->
<!--        </div>-->
    </div>
    <a id="_cc-access-wg" class="_cc-fab <?= $open && $access ? 'is-visible' : '' ?>" style="<?= $access ? '' : 'background: #d5b24c' ?>">
        <i class="fa fa-comments-o"></i>
        <?php if ($totalRequest): ?>
            <span class="_cc_total_request_wrapper">
                <?= $totalRequest ?>
            </span>
            <span class="circle" style="animation-delay: 0s"></span>
            <span class="circle" style="animation-delay: 1s"></span>
            <span class="circle" style="animation-delay: 2s"></span>
            <span class="circle" style="animation-delay: 3s"></span>
        <?php endif; ?>
    </a>
</div>
<?php // yii\widgets\Pjax::end() ?>

<?php
$js = <<<JS
    
let _ccWgStatus = localStorage.getItem('_cc_wg_status');
let _access = {$totalRequest} > 0 ? true : false;
if (_ccWgStatus === 'true' && _access) {
    toggleClientChatAccess();
}

window.addEventListener('storage', function (event) {
    if (event.key === '_cc_wg_status') {
        let _ccWgStatus = localStorage.getItem('_cc_wg_status');
        if (_ccWgStatus === 'true') {
            toggleClientChatAccess(true);
        } else {
            toggleClientChatAccess(false);
        }
    }
});
// $(document).on('click', '._cc_chevron', function () {
//     $(this).closest('._cc-box-item').toggleClass('active');
// });
JS;

$this->registerJs($js);
