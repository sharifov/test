<?php

use common\models\Call;
use common\models\Email;
use common\models\Employee;
use common\models\Sms;
use frontend\widgets\communication\CommunicationListItemWidget;
use src\helpers\call\CallHelper;
use src\helpers\phone\MaskPhoneHelper;
use src\model\clientChat\entity\ClientChat;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $disableMasking bool
 */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model array */
?>

<?php

/** @var Employee $user */
$user = Yii::$app->user->identity;

$fromType = 'client';

?>

<?php //php \yii\helpers\VarDumper::dump($model, 10, true)?>

<?php if ($model['type'] === 'voice') :
    $call = Call::findOne($model['id']);
    if ($call) :
        if ($call->isStatusCompleted()) {
            $statusClass = 'success';
            $statusTitle = 'COMPLETED - ' . Yii::$app->formatter->asDatetime(strtotime($call->c_created_dt) + (int) $call->c_call_duration);
        } elseif ($call->isStatusCanceled()) {
            $statusClass = 'error';
            $statusTitle = 'CANCELED';
        } else {
            $statusClass = 'sent';
            $statusTitle = $call->c_call_status; //'INIT';
        }

        if ($call->c_id) {
            $statusTitle .= ' - Call ID: ' . $call->c_id;
        }

        if ($call->isIn()) {
            $fromType = 'client';
        } else {
            $fromType = 'system';
        }
        ?>
    <div class="chat__message chat__message--<?=$fromType?> chat__message--phone">
        <div class="chat__icn"><i class="fa fa-phone"></i></div>
        <i class="chat__status chat__status--<?=$statusClass?> fa fa-circle" data-toggle="tooltip" title="<?=Html::encode($statusTitle)?>" data-placement="right" data-original-title="<?=Html::encode($statusTitle)?>"></i>
        <div class="chat__message-heading">

            <?php if ($call->isIn()) :?>
                <div class="chat__sender">
                    <i class="fa fa-phone" title="<?=Html::encode(MaskPhoneHelper::masking($call->c_from, $disableMasking))?>"></i> <?php //php $call->cClient ? Html::encode($call->cClient->full_name) : ''?>
                    to <b><?=($call->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($call->cCreatedUser->username) : '-') ?></b>
                </div>
            <?php else : ?>
                <div class="chat__sender">
                    from "<b title="<?=Html::encode($call->c_from)?>"><?=($call->isClientNotification() ? 'system notification' : ($call->cCreatedUser ? Html::encode($call->cCreatedUser->username) : '-')) ?></b>" to <i class="fa fa-phone" title="<?=Html::encode(MaskPhoneHelper::masking($call->c_to, $disableMasking))?>"></i>
                    <?=Html::encode(MaskPhoneHelper::masking($call->c_to, $disableMasking))?>
                </div>
            <?php endif;?>

            <div class="chat__date">
                <?php if ($user->isAdmin()) :?>
                    Id: <u title="SID: <?=Html::encode($call->c_call_sid)?>"><?=Html::a($call->c_id, ['call/view', 'id' => $call->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u>,
                <?php endif; ?>
                <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($call->c_created_dt))?></div>
        </div>
        <div class="card-body">
            <?php if ($call->recordingUrl) :?>
                <?= CallHelper::displayAudioBtn($call->recordingUrl, 'i:s', $call->c_recording_duration) ?>
            <?php else : ?>
                <div><?=$call->getStatusIcon()?>  <?=$call->getStatusName()?></div>
            <?php endif;?>
            <div><?=$call->c_call_duration > 0 ? 'Duration: ' . Yii::$app->formatter->asDuration($call->c_call_duration) : ''?></div>

            <?php if ($call->calls) :?>
                <?php \src\helpers\communication\CommunicationHelper::renderChildCallsRecursive($call->calls)?>
            <?php endif;?>

        </div>
    </div>
    <?php endif;?>
<?php endif;?>

<?php if ($model['type'] === 'email') :?>
    <?= CommunicationListItemWidget::widget(['type' => $model['type'], 'id' => $model['id'], 'disableMasking' => $disableMasking]);?>
<?php endif;?>

<?php if ($model['type'] === 'sms') :
        $sms = Sms::findOne($model['id']);
    if ($sms) :
        if ($sms->s_status_id == Sms::STATUS_DONE) {
            $statusClass = 'success';
            $statusTitle = 'DELIVERED - ' . ($sms->s_status_done_dt ? Yii::$app->formatter->asDatetime(strtotime($sms->s_status_done_dt)) : Yii::$app->formatter->asDatetime(strtotime($sms->s_updated_dt)));
        } elseif ($sms->s_status_id == Sms::STATUS_ERROR || $sms->s_status_id == Sms::STATUS_CANCEL) {
            $statusClass = 'error';
            $statusTitle = 'ERROR - ' . $sms->s_error_message;
        } else {
            $statusClass = 'sent';
            $statusTitle = 'SENT - ComID: ' . $sms->s_communication_id;
        }
        ?>
        <div class="chat__message chat__message--<?=($sms->s_type_id == Sms::TYPE_INBOX ? 'client' : 'system')?> chat__message--sms">
        <div class="chat__icn"><i class="fas fa-sms"> </i></div>

        <i class="chat__status chat__status--<?=$statusClass?> fa fa-circle" data-toggle="tooltip" title="<?=Html::encode($statusTitle)?>" data-placement="left" data-original-title="<?=Html::encode($statusTitle)?>"></i>
        <div class="chat__message-heading">
        <?php if ($sms->s_type_id == Sms::TYPE_INBOX) :?>
                <div class="chat__sender">SMS from <strong><?=Html::encode(MaskPhoneHelper::masking($sms->s_phone_from, $disableMasking))?></strong> to <strong><?=Html::encode($sms->s_phone_to)?></strong></div>
        <?php else : ?>
                <div class="chat__sender">SMS from <strong><?=($sms->sCreatedUser ? Html::encode($sms->sCreatedUser->username) : '-') ?>, (<?=Html::encode($sms->s_phone_from)?>)</strong> to <strong><?=Html::encode(MaskPhoneHelper::masking($sms->s_phone_to, $disableMasking))?></strong></div>
        <?php endif; ?>
            <div class="chat__date"><?=Yii::$app->formatter->asDatetime(strtotime($sms->s_created_dt))?> <?=$sms->s_language_id ? '(' . $sms->s_language_id . ')' : ''?></div> <?php //11:01AM | June 9?>
        </div>
        <div class="card-body" style="word-wrap: break-word">
        <?=nl2br(Html::encode($sms->s_sms_text))?>
        </div>
    </div>
    <?php endif;?>
<?php endif;?>

<?php
if ($model['type'] === 'chat') {
    if ($chat = ClientChat::find()->andWhere(['cch_id' => $model['id']])->one()) {
        echo $this->render('../../partial/_communication_chat_block', ['chat' => $chat]);
    }
}
?>
