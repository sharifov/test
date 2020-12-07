<?php

use frontend\helpers\EmailHelper;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Email;
use common\models\Sms;
use common\models\Call;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model array */
?>

<?php
    $fromType = 'client';
?>

<?php //php \yii\helpers\VarDumper::dump($model, 10, true) ?>

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
                <div class="chat__sender">Call from (<strong><?=Html::encode($call->c_from)?>) </strong> to (<strong><?=Html::encode($call->c_to)?></strong>)</div>
            <?php elseif ($call->isOut()) : ?>
                <div class="chat__sender">Call from <b><?=($call->cCreatedUser ? Html::encode($call->cCreatedUser->username) : '-') ?></b>, (<strong><?=Html::encode($call->c_from)?>) </strong> to (<strong><?=Html::encode($call->c_to)?></strong>)</div>
            <?php endif;?>

            <div class="chat__date"><?=Yii::$app->formatter->asDatetime(strtotime($call->c_created_dt))?></div> <?php //11:01AM | June 9?>
        </div>
        <div class="card-body">
            <?php if ($call->recordingUrl) :?>
                <audio controls="controls" controlsList="nodownload" class="chat__audio">
                    <source src="<?=$call->recordingUrl?>" type="audio/mpeg">
                    Your browser does not support the audio element
                </audio>
            <?php else : ?>
                <div><i class="fa fa-volume-off"></i> ... <?=$call->c_call_status?></div>
            <?php endif;?>
            <div><?=$call->c_call_duration > 0 ? 'Duration: ' . Yii::$app->formatter->asDuration($call->c_call_duration) : ''?></div>
        </div>
    </div>
    <?php endif;?>
<?php endif;?>

<?php if ($model['type'] === 'email') :
        $mail = Email::findOne($model['id']);
    if ($mail) :
        if ($mail->e_status_id == Email::STATUS_DONE) {
            $statusClass = 'success';
            $statusTitle = 'DONE - ' . ($mail->e_status_done_dt ? Yii::$app->formatter->asDatetime(strtotime($mail->e_status_done_dt)) : Yii::$app->formatter->asDatetime(strtotime($mail->e_updated_dt)));
        } elseif ($mail->e_status_id == Email::STATUS_ERROR || $mail->e_status_id == Email::STATUS_CANCEL) {
            $statusClass = 'error';
            $statusTitle = 'ERROR - ' . $mail->e_error_message;
        } else {
            $statusClass = 'sent';
            $statusTitle = 'SENT - ComID: ' . $mail->e_communication_id;
        }
        ?>

        <div class="chat__message chat__message--<?=($mail->e_type_id == Email::TYPE_INBOX ? 'client' : 'system')?> chat__message--email">
            <div class="chat__icn"><i class="fa fa-envelope-o"></i></div>
            <i class="chat__status chat__status--<?=$statusClass?> fa fa-circle" data-toggle="tooltip" title="<?=Html::encode($statusTitle)?>" data-placement="right" data-original-title="<?=Html::encode($statusTitle)?>"></i>
            <div class="chat__message-heading">
            <?php if ($mail->e_type_id == Email::TYPE_INBOX) :?>
                    <div class="chat__sender">Email from (<?=Html::encode($mail->e_email_from_name)?> <<strong><?=Html::encode($mail->e_email_from)?>> )</strong>
                            to (<?=Html::encode($mail->e_email_to_name)?> <<strong><?=Html::encode($mail->e_email_to)?></strong>>)</div>
            <?php else : ?>
                    <div class="chat__sender">Email from <?=($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') ?>, (<?=Html::encode($mail->e_email_from_name)?> <<strong><?=Html::encode($mail->e_email_from)?></strong>>) to
                        (<?=Html::encode($mail->e_email_to_name)?> <<strong><?=Html::encode($mail->e_email_to)?></strong>>)</div>
            <?php endif;?>
                <div class="chat__date"><?=Yii::$app->formatter->asDatetime(strtotime($mail->e_created_dt))?> <?=$mail->e_language_id ? '(' . $mail->e_language_id . ')' : ''?></div> <?php //11:01AM | June 9?>
            </div>
            <div class="card-body">
                <h5 class="chat__subtitle"><?= wordwrap(Html::encode($mail->e_email_subject), 60, '<br />', true) ?></h5>
                <div class="">
                <?php echo \yii\helpers\StringHelper::truncate(Email::stripHtmlTags($mail->getEmailBodyHtml()), 300, '...', null, true)?>
                </div>
            <?php if (Auth::can('email/view', ['email' => $mail])) : ?>
                    <div class="chat__message-footer">
                        <?= EmailHelper::renderDetailButton($mail) ?>
                    </div>
            <?php endif;?>
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
                <div class="chat__sender">SMS from <strong><?=Html::encode($sms->s_phone_from)?></strong> to <strong><?=Html::encode($sms->s_phone_to)?></strong></div>
        <?php else : ?>
                <div class="chat__sender">SMS from <strong><?=($sms->sCreatedUser ? Html::encode($sms->sCreatedUser->username) : '-') ?>, (<?=Html::encode($sms->s_phone_from)?>)</strong> to <strong><?=Html::encode($sms->s_phone_to)?></strong></div>
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
