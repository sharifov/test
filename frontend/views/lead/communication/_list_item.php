<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\models\Email;
use \common\models\Sms;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model array */
?>

<?php
    $fromType = 'client';
?>

<?//php \yii\helpers\VarDumper::dump($model, 10, true) ?>

<?php if($model['type'] === 'voice'): ?>
    <div class="chat__message chat__message--<?=$fromType?> chat__message--phone">
    <div class="chat__icn"><i class="fa fa-phone"></i></div>
    <i class="chat__status chat__status--sent fa fa-circle" data-toggle="tooltip" title="" data-placement="left" data-original-title="SENT"></i>
    <div class="chat__message-heading">
        <div class="chat__sender">Call from <strong>Agent Mary</strong> to <strong>+37366889955</strong></div>
        <div class="chat__date">11:01AM | June 9</div>
    </div>
    <div class="panel-body">
        <audio controls="controls" class="chat__audio">
            <source src="audio.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
</div>
<?php endif;?>

<?php if($model['type'] === 'email'):
        $mail = Email::findOne($model['id']);
        if($mail):

        if($mail->e_status_id == Email::STATUS_DONE) {
            $statusClass = 'success';
            $statusTitle = 'DONE - ' . Yii::$app->formatter->asDatetime(strtotime($mail->e_status_done_dt));
        } elseif($mail->e_status_id == Email::STATUS_ERROR || $mail->e_status_id == Email::STATUS_CANCEL) {
            $statusClass = 'error';
            $statusTitle = 'ERROR - '. $mail->e_error_message;
        } else {
            $statusClass = 'sent';
            $statusTitle = 'SENT - ComID: '. $mail->e_communication_id;
        }
    ?>

        <div class="chat__message chat__message--<?=($mail->e_type_id == Email::TYPE_INBOX ? 'client' : 'system')?> chat__message--email">
            <div class="chat__icn"><i class="fa fa-envelope-open"></i></div>
            <i class="chat__status chat__status--<?=$statusClass?> fa fa-circle" data-toggle="tooltip" title="<?=Html::encode($statusTitle)?>" data-placement="right" data-original-title="<?=Html::encode($statusTitle)?>"></i>
            <div class="chat__message-heading">
                <?php if($mail->e_type_id == Email::TYPE_INBOX):?>
                    <div class="chat__sender">Email from <strong><?=($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') ?>, (<?=Html::encode($mail->e_email_from)?>)</strong> to  (<strong><?=Html::encode($mail->e_email_to)?></strong>)</div>
                <? else: ?>
                    <div class="chat__sender">Email from (<strong><?=Html::encode($mail->e_email_from)?></strong>) to (<strong><?=Html::encode($mail->e_email_to)?></strong>)</div>
                <?php endif;?>
                <div class="chat__date"><?=Yii::$app->formatter->asDatetime(strtotime($mail->e_created_dt))?> <?=$mail->e_language_id ? '('.$mail->e_language_id.')' : ''?></div> <?php //11:01AM | June 9?>
            </div>
            <div class="panel-body">
                <h5 class="chat__subtitle"><?=Html::encode($mail->e_email_subject)?></h5>
                <div class="">
                    <?php echo \yii\helpers\StringHelper::truncate(Email::strip_html_tags($mail->e_email_body_html), 300, '...', null, true)?>
                </div>
                <div class="chat__message-footer">
                    <?=Html::a('<i class="fa fa-search-plus"></i> Details', '#', ['class' => 'chat__details', 'data-id' => $mail->e_id])?>
                </div>
            </div>
        </div>
        <?php endif;?>
<?php endif;?>

<?php if($model['type'] === 'sms'):
        $sms = Sms::findOne($model['id']);
        if($sms):

            if($sms->s_status_id == Sms::STATUS_DONE) {
                $statusClass = 'success';
                $statusTitle = 'DELIVERED - ' . Yii::$app->formatter->asDatetime(strtotime($sms->s_status_done_dt));
            } elseif($sms->s_status_id == Sms::STATUS_ERROR || $sms->s_status_id == Sms::STATUS_CANCEL) {
                $statusClass = 'error';
                $statusTitle = 'ERROR - '. $sms->s_error_message;
            } else {
                $statusClass = 'sent';
                $statusTitle = 'SENT - ComID: '. $sms->s_communication_id;
            }
    ?>
        <div class="chat__message chat__message--<?=($sms->s_type_id == Sms::TYPE_INBOX ? 'client' : 'system')?> chat__message--sms">
        <div class="chat__icn"><i class="fa fa-comments-o"></i></div>

        <i class="chat__status chat__status--<?=$statusClass?> fa fa-circle" data-toggle="tooltip" title="<?=Html::encode($statusTitle)?>" data-placement="left" data-original-title="<?=Html::encode($statusTitle)?>"></i>
        <div class="chat__message-heading">
            <?php if($sms->s_type_id == Sms::TYPE_INBOX):?>
                <div class="chat__sender">SMS from <strong><?=Html::encode($sms->s_phone_from)?></strong> to <strong><?=Html::encode($sms->s_phone_to)?></strong></div>
            <? else: ?>
                <div class="chat__sender">SMS from <strong><?=($sms->sCreatedUser ? Html::encode($sms->sCreatedUser->username) : '-') ?>, (<?=Html::encode($sms->s_phone_from)?>)</strong> to <strong><?=Html::encode($sms->s_phone_to)?></strong></div>
            <?php endif; ?>
            <div class="chat__date"><?=Yii::$app->formatter->asDatetime(strtotime($sms->s_created_dt))?> <?=$sms->s_language_id ? '('.$sms->s_language_id.')' : ''?></div> <?php //11:01AM | June 9?>
        </div>
        <div class="panel-body">
            <?=nl2br(Html::encode($sms->s_sms_text))?>
        </div>
    </div>
    <?php endif;?>
<?php endif;?>
