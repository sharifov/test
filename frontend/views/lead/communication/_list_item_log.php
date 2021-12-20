<?php

use common\models\Employee;
use frontend\helpers\EmailHelper;
use sales\auth\Auth;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;
use common\models\Email;
use common\models\Sms;
use sales\helpers\email\MaskEmailHelper;
use sales\helpers\phone\MaskPhoneHelper;
use modules\email\src\abac\dto\EmailAbacDto;
use modules\email\src\abac\EmailAbacObject;

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


<?php if ($model['type'] === 'voice') :
    $call = CallLog::findOne($model['id']);
    if ($call) :
        if ($call->isStatusCompleted()) {
            $statusClass = 'success';
            $statusTitle = 'COMPLETED - ' . Yii::$app->formatter->asDatetime(strtotime($call->cl_call_created_dt) + (int) $call->cl_duration);
        } elseif ($call->isStatusCanceled()) {
            $statusClass = 'error';
            $statusTitle = 'CANCELED';
        } else {
            $statusClass = 'sent';
            $statusTitle = CallLogStatus::getName($call->cl_status_id); //'INIT';
        }

        $statusTitle .= ' - Call ID: ' . $call->cl_id;

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
                        <span title="<?=Html::encode(MaskPhoneHelper::masking($call->cl_phone_from, $disableMasking))?>">
                            <i class="fa fa-phone"></i> <?= $call->client ? Html::encode($call->client->full_name ?? '') : 'Client'?>
                        </span>
                        to
                        <span>
                            <b><?=($call->user ? '<i class="fa fa-user"></i> ' . Html::encode($call->user->username) : 'Agent') ?></b>
                        </span>
                    </div>
                <?php else : ?>
                    <div class="chat__sender">
                        from "<b title="<?=Html::encode($call->cl_phone_from)?>"><?=($call->isClientNotification() ? 'system notification' : ($call->user ? Html::encode($call->user->username) : 'Agent')) ?></b>" to <i class="fa fa-phone" title="<?=Html::encode(MaskPhoneHelper::masking($call->cl_phone_to, $disableMasking))?>"></i>
                        <?=Html::encode(MaskPhoneHelper::masking($call->cl_phone_to, $disableMasking))?>
                    </div>
                <?php endif;?>

                <div class="chat__date">
                    <?php if ($user->isAdmin()) :?>
                        Id: <u title="SID: <?=Html::encode($call->cl_call_sid)?>"><?=Html::a($call->cl_id, ['call/view', 'id' => $call->cl_id], ['target' => '_blank', 'data-pjax' => 0])?></u>,
                    <?php endif; ?>
                    <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($call->cl_call_created_dt))?></div>
            </div>
            <div class="card-body">
                <?php //  if($call->record && $call->record->clr_record_sid):?>

                <?php //  else: ?>
                    <div><?php //$call->getStatusIcon()?>  <?php // CallLogStatus::getName($call->cl_status_id) ?></div>
                <?php // endif;?>
                <div><?php // $call->cl_duration > 0 ? 'Duration: ' . Yii::$app->formatter->asDuration($call->cl_duration) : ''?></div>

                <?= $this->render('_list_call_recursive_log', [
                    'callList' => $call->childCalls
                ]) ?>


            </div>
        </div>
    <?php endif;?>
<?php endif;?>

<?php if ($model['type'] === 'email') :
    $mail = Email::findOne($model['id']);
    if ($mail) :
        $unsubscribedEmails = array_column($mail->eProject ? $mail->eProject->emailUnsubscribes : [], 'eu_email');
        ?>

        <div class="chat__message chat__message--<?=($mail->e_type_id == Email::TYPE_INBOX ? 'client' : 'system')?> chat__message--email">
            <div class="chat__icn"><i class="fa fa-envelope-o"></i></div>
            <?= EmailHelper::getIconForCommunicationBlock($mail) ?>
            <div class="chat__message-heading">
                <?php if ($mail->e_type_id == Email::TYPE_INBOX) :?>
                    <div class="chat__sender">Email from (<?=Html::encode($mail->e_email_from_name)?> <<strong><?=Html::encode(MaskEmailHelper::masking($mail->e_email_from, $disableMasking))?>> )</strong>
                        to (<?=Html::encode($mail->e_email_to_name)?> <<strong><?=Html::encode($mail->e_email_to)?></strong>>)</div>
                <?php else : ?>
                    <div class="chat__sender">Email from <?=($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') ?>, (<?=Html::encode($mail->e_email_from_name)?> <<strong><?=Html::encode($mail->e_email_from)?></strong>>) to
                        (<?=Html::encode($mail->e_email_to_name)?> <<strong class="<?= in_array($mail->e_email_to, $unsubscribedEmails) ? 'text-line-through' : ''?>" ><?=Html::encode(MaskEmailHelper::masking($mail->e_email_to, $disableMasking))?></strong>>)</div>
                <?php endif;?>
                <div class="chat__date"><?=Yii::$app->formatter->asDatetime(strtotime($mail->e_created_dt))?> <?=$mail->e_language_id ? '(' . $mail->e_language_id . ')' : ''?></div> <?php //11:01AM | June 9?>
            </div>
            <div class="card-body">
                <h5 class="chat__subtitle" style="word-break: break-all;"><?= wordwrap(Html::encode($mail->e_email_subject), 60, '<br />', true)?></h5>
                <div class="">
                    <?php echo \yii\helpers\StringHelper::truncate(Email::stripHtmlTags($mail->getEmailBodyHtml()), 300, '...', null, true)?>
                </div>
                <?php /*if (Auth::can('email/view', ['email' => $mail])) : */?>

                <?php /** @abac new EmailAbacDto($mail), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS, Restrict access to button email details on lead*/ ?>
                <?php if (Yii::$app->abac->can(new EmailAbacDto($mail), EmailAbacObject::ACT_VIEW, EmailAbacObject::ACTION_ACCESS)) : ?>
                    <div class="chat__message-footer">
                        <?= EmailHelper::renderDetailButton($mail) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif;?>
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
            <div class="chat__icn"><i class="fas fa-sms"></i></div>

            <i class="chat__status chat__status--<?=$statusClass?> fa fa-circle" data-toggle="tooltip" title="<?=Html::encode($statusTitle)?>" data-placement="left" data-original-title="<?=Html::encode($statusTitle)?>"></i>
            <div class="chat__message-heading">
                <?php if ($sms->s_type_id == Sms::TYPE_INBOX) :?>
                    <div class="chat__sender">SMS from <strong><?=Html::encode(MaskPhoneHelper::masking($sms->s_phone_from, $disableMasking))?></strong> to <strong><?=Html::encode($sms->s_phone_to)?></strong></div>
                <?php else : ?>
                    <div class="chat__sender">SMS from <strong><?=($sms->sCreatedUser ? Html::encode($sms->sCreatedUser->username) : '-') ?>, (<?=Html::encode($sms->s_phone_from)?>)</strong> to <strong><?=Html::encode(MaskPhoneHelper::masking($sms->s_phone_to, $disableMasking))?></strong></div>
                <?php endif; ?>
                <div class="chat__date"><?=Yii::$app->formatter->asDatetime(strtotime($sms->s_created_dt))?> <?=$sms->s_language_id ? '(' . $sms->s_language_id . ')' : ''?></div> <?php //11:01AM | June 9?>
            </div>
            <div class="card-body">
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
