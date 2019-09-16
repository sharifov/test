<?php

use yii\helpers\Html;
use \common\models\Email;
use \common\models\Sms;
use \common\models\Call;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model array */
?>

<?php
    $fromType = 'client';
?>

<?//php \yii\helpers\VarDumper::dump($model, 10, true) ?>

<?php if($model['type'] === 'voice'):

    $call = Call::findOne($model['id']);
    if($call):

        if($call->isCompleted()) {
            $statusClass = 'success';
            $statusTitle = 'COMPLETED - ' . Yii::$app->formatter->asDatetime(strtotime($call->c_created_dt) + (int) $call->c_call_duration);
        } elseif($call->isCanceled()) {
            $statusClass = 'error';
            $statusTitle = 'CANCELED';
        } else {
            $statusClass = 'sent';
            $statusTitle = $call->c_call_status; //'INIT';
        }

        if($call->c_id) {
            $statusTitle .= ' - Call ID: '. $call->c_id;
        }

        if($call->isIn()) {
            $fromType = 'client';
        } else {
            $fromType = 'system';
        }
    ?>
    <div class="chat__message chat__message--<?=$fromType?> chat__message--phone">
        <div class="chat__icn"><i class="fa fa-phone"></i></div>
        <i class="chat__status chat__status--<?=$statusClass?> fa fa-circle" data-toggle="tooltip" title="<?=Html::encode($statusTitle)?>" data-placement="right" data-original-title="<?=Html::encode($statusTitle)?>"></i>
        <div class="chat__message-heading">

            <?php if($call->isIn()):?>
                <div class="chat__sender">
                    <i class="fa fa-phone" title="<?=Html::encode($call->c_from)?>"></i> <?//php $call->cClient ? Html::encode($call->cClient->full_name) : ''?>
                    to <b><?=($call->cCreatedUser ? '<i class="fa fa-user"></i> '.Html::encode($call->cCreatedUser->username) : '-') ?></b>
                </div>
            <?php else: ?>
                <div class="chat__sender">from "<b title="<?=Html::encode($call->c_from)?>"><?=($call->cCreatedUser ? Html::encode($call->cCreatedUser->username) : '-') ?></b>" to <i class="fa fa-phone" title="<?=Html::encode($call->c_to)?>"></i></div>
            <?php endif;?>

            <div class="chat__date">
                <?php if(Yii::$app->user->identity->isAdmin()):?>
                    Id: <u title="SID: <?=Html::encode($call->c_call_sid)?>"><?=Html::a($call->c_id, ['call/view', 'id' => $call->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u>,
                <?php endif; ?>
                <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($call->c_created_dt))?></div>
        </div>
        <div class="panel-body">
            <?php if($call->c_recording_url):?>


                <?=Html::button(gmdate('i:s', $call->c_recording_duration) . ' <i class="fa fa-volume-up"></i>',
                    ['class' => 'btn btn-' . ($call->c_recording_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $call->c_recording_url]) ?>

                <?/*<audio controls="controls" controlsList="nodownload" class="chat__audio" style="height: 25px; width: 100%">
                    <source src="<?=$call->c_recording_url?>" type="audio/mpeg">
                </audio>*/?>
            <?php else: ?>
                <div><?=$call->getStatusIcon()?>  <?=$call->getStatusName()?></div>
            <?php endif;?>
            <div><?=$call->c_call_duration > 0 ? 'Duration: ' . Yii::$app->formatter->asDuration($call->c_call_duration) : ''?></div>

            <?php if ($call->calls):?>

               <table class="table table-bordered table-hover">
                            <?php foreach ($call->calls as $callItem):?>
                                <tr>
                                    <?php if (Yii::$app->user->identity->isAdmin()):?>
                                        <td style="width:50px" rowspan="2">
                                            <u title="SID: <?=Html::encode($callItem->c_call_sid)?>"><?=Html::a($callItem->c_id, ['call/view', 'id' => $callItem->c_id], ['target' => '_blank', 'data-pjax' => 0])?></u><br>
                                        </td>
                                    <?php endif; ?>
                                    <td colspan="3">
                                        <?php if ($callItem->c_recording_url):?>
                                            <?=Html::button(gmdate('i:s', $callItem->c_recording_duration) . ' <i class="fa fa-volume-up"></i>',
                                                ['class' => 'btn btn-' . ($callItem->c_recording_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $callItem->c_recording_url]) ?>
                                        
                                            <?/*<audio controls="controls" controlsList="nodownload" class="chat__audio" style="height: 25px; width: 100%">
                                                <source src="<?=$callItem->c_recording_url?>" type="audio/mpeg">
                                            </audio>*/?>
                                        <?php else: ?>

                                        <?php endif;?>
                                    </td>
                                </tr>
                                <tr>

                                    <td class="text-center">
                                        <i class="fa fa-clock-o"></i> <?=Yii::$app->formatter->asDatetime(strtotime($callItem->c_created_dt), 'php:H:i:s')?>
                                        <br>&nbsp;&nbsp;<?=Yii::$app->formatter->asRelativeTime(strtotime($callItem->c_created_dt))?>
                                    </td>
                                    <td class="text-left">

                                        <div><?=$callItem->c_call_duration > 0 ? 'duration: ' . Yii::$app->formatter->asDuration($callItem->c_call_duration) : ''?></div>

                                        <?=$callItem->getStatusIcon()?>  <?=$callItem->getStatusName()?>
<!--                                        <br>--><?//=$callItem->getStatusName2()?><!--<br>-->

                                    </td>
<!--                                    <td class="text-center">-->
<!---->
<!--                                        --><?php
//                                        $sec = 0;
//                                        if($callItem->c_updated_dt) {
//
//                                            if($callItem->isIvr() || $callItem->isRinging() || $callItem->isInProgress() || $callItem->isQueue()) {
//                                                $sec = time() - strtotime($callItem->c_updated_dt);
//                                            } else {
//                                                $sec = $callItem->c_call_duration ?: strtotime($callItem->c_updated_dt) - strtotime($callItem->c_created_dt);
//                                            }
//                                        }
//                                        ?>
<!---->
<!--                                        --><?php //if ($callItem->isIvr() || $callItem->isRinging() || $callItem->isInProgress() || $callItem->isQueue()):?>
<!--                                            <span class="badge badge-warning timer" data-sec="--><?//=$sec?><!--" data-control="start" data-format="%M:%S" title="--><?//=Yii::$app->formatter->asDuration($sec)?><!--">-->
<!--                        00:00-->
<!--                    </span>-->
<!--                                        --><?php //else: ?>
<!--                                            <span class="badge badge-primary timer" data-sec="--><?//=$sec?><!--" data-control="pause" data-format="%M:%S" title="--><?//=Yii::$app->formatter->asDuration($sec)?><!--">-->
<!--                        00:00-->
<!--                    </span>-->
<!---->
<!--                                        --><?php //endif;?>
<!---->
<!--                                    </td>-->
                                    <td class="text-center" style="width:110px">
                                        <?php if($callItem->isIn()):?>
                                            <div>
                                                <?php if($callItem->c_created_user_id):?>
                                                    <i class="fa fa-user fa-border"></i><br>
                                                    <?=Html::encode($callItem->cCreatedUser->username)?>
                                                <?php else: ?>
                                                    <i class="fa fa-phone fa-border"></i><br>
                                                    <?=$callItem->c_to?>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div>
                                                <i class="fa fa-male text-info fa-border"></i>
                                            </div>
                                            <?=$callItem->c_to?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </table>

            <?php endif;?>


        </div>
    </div>
    <?php endif;?>
<?php endif;?>

<?php if($model['type'] === 'email'):
        $mail = Email::findOne($model['id']);
        if($mail):

        if($mail->e_status_id == Email::STATUS_DONE) {
            $statusClass = 'success';
            $statusTitle = 'DONE - ' . ($mail->e_status_done_dt ? Yii::$app->formatter->asDatetime(strtotime($mail->e_status_done_dt)) : Yii::$app->formatter->asDatetime(strtotime($mail->e_updated_dt)));
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
                    <div class="chat__sender">Email from (<?=Html::encode($mail->e_email_from_name)?> <<strong><?=Html::encode($mail->e_email_from)?>> )</strong>
                            to (<?=Html::encode($mail->e_email_to_name)?> <<strong><?=Html::encode($mail->e_email_to)?></strong>>)</div>
                <?php else: ?>
                    <div class="chat__sender">Email from <?=($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') ?>, (<?=Html::encode($mail->e_email_from_name)?> <<strong><?=Html::encode($mail->e_email_from)?></strong>>) to
                        (<?=Html::encode($mail->e_email_to_name)?> <<strong><?=Html::encode($mail->e_email_to)?></strong>>)</div>
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
                $statusTitle = 'DELIVERED - ' . ($sms->s_status_done_dt ? Yii::$app->formatter->asDatetime(strtotime($sms->s_status_done_dt)) : Yii::$app->formatter->asDatetime(strtotime($sms->s_updated_dt)));
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
