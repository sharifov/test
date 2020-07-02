<?php

use frontend\widgets\clientChat\ClientChatAsset;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;

/** @var $this \yii\web\View */
/** @var $access ClientChatUserAccess[] */
/** @var $isPjax bool */

ClientChatAsset::register($this);

$accessUrl = \yii\helpers\Url::to('/client-chat/access-manage');
?>


<?php yii\widgets\Pjax::begin(['id' => 'client-chat-box-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => []])?>
<div class="_cc-fabs">
    <div class="_cc-box <?= $isPjax && $access ? 'is-visible' : '' ?>">
        <div class="_сс-box-header" style="<?= $access ? 'background: #78c286' : 'background: #d5b24c' ?>">
            <div class="_cc-box-option">
                <div class="header_img">
					<?=\yii\helpers\Html::img('/img/user.png')?>
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
                                    <?= $item->ccuaCch->cchClient ? $item->ccuaCch->cchClient->full_name : 'ClientName' ?>
                                </span>

                                <?php if ($item->ccuaCch->cchClient && $item->ccuaCch->cchClient->clientEmails): ?>
                                    <?php foreach($item->ccuaCch->cchClient->clientEmails as $email): ?>
                                        <span class="_cc-client-email">
                                            <i class="fa fa-envelope"></i>
                                            <code><?= $email->email ?></code>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if ($item->ccuaCch->cchClient && $item->ccuaCch->cchClient->clientPhones): ?>
                                    <?php foreach($item->ccuaCch->cchClient->clientPhones as $phone): ?>
                                        <span class="_cc-client-phone">
                                            <i class="fa fa-phone"></i>
                                            <code><?= $phone->phone ?></code>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="_cc-data">
                                <?php if ($item->ccuaCch->cchDep): ?>
                                    <span class="label label-default"><?= $item->ccuaCch->cchDep->dep_name ?></span>
                                <?php endif; ?>

                                <?php if ($item->ccuaCch->cchProject): ?>
                                    <span class="label label-default"><?= $item->ccuaCch->cchProject->name ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="_cc-action">
                                <button class="btn btn-sm btn-success _cc-access-action" data-cch-id="<?= $item->ccua_cch_id ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_ACCEPT ?>"><i class="fa fa-check"></i> Accept</button>
                                <button class="btn btn-sm btn-warning _cc-access-action" data-cch-id="<?= $item->ccua_cch_id ?>" data-ajax-url="<?= $accessUrl ?>" data-access-action="<?= ClientChatUserAccess::STATUS_SKIP ?>"><i class="fa fa-close"></i> Skip</button>
                            </div>
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
    <a id="_cc-access-wg" class="_cc-fab " style="<?= $access ? '' : 'background: #d5b24c' ?>">
        <i class="fa fa-comments-o"></i>
    </a>
</div>
<?php yii\widgets\Pjax::end() ?>

<?php
$accessExist = count($access);
$js = <<<JS
 if ({$accessExist}) {
    toggleClientChatAccess();
 }
JS;

$this->registerJs($js);
