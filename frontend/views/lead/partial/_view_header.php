<?php

/**
 * @var $this \yii\web\View
 * @var $title string
 * @var $lead Lead
 */

use common\models\Lead;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\helpers\lead\LeadHelper;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use yii\helpers\Html;

$bundle = \frontend\assets\TimerAsset::register($this);
$timerExtraCss = 'vertical-align: 3px;font-size: 12px; margin-left: 5px;';
?>

<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title">
                <?= Html::encode($title) ?>
                <?php
                if ($lead->clone_id && $cloneLead = $lead->clone) {
                    echo Html::a('(Cloned from ' . $lead->clone_id . ' )', ['lead/view', 'gid' => $cloneLead->gid], ['title' => 'Clone reason: ' . $lead->description]);
                }
                ?>
                <?= $lead->getStatusLabel() ?>
                <?php if ($lead->isSnooze()) : ?>
                    <?= LeadHelper::displaySnoozeFor($lead, time(), 'vertical-align: 3px;font-size: 12px; margin-left: 5px;') ?>
                <?php endif; ?>

                <?php if (LeadBusinessExtraQueueService::ffIsEnabled()) : ?>
                    <?= LeadHelper::displayBusinessExtraQueueTimerIfExists($lead, $timerExtraCss) ?>
                <?php endif; ?>
                <?php if (LeadHelper::isShowLppTimer($lead)) : ?>
                    <?= LeadHelper::displayLeadPoorProcessingTimer($lead->minLpp->lpp_expiration_dt, $lead->minLpp->lppLppd->lppd_name, $timerExtraCss) ?>
                <?php elseif ($lead->l_expiration_dt) : ?>
                    <?php if (LeadHelper::expiredLead($lead)) : ?>
                        <span
                            class="label status-label bg-red"
                            data-toggle="tooltip"
                            data-original-title="Expiration date: <?php echo Yii::$app->formatter->asDatetime(strtotime($lead->l_expiration_dt)) ?>">
                                <i class="fa fa-clock-o"></i> Expired</span>
                    <?php else : ?>
                        <span
                            class="label status-label bg-orange box-clock-expiration"
                            data-toggle="tooltip"
                            data-original-title="Expiration date: <?php echo Yii::$app->formatter->asDatetime(strtotime($lead->l_expiration_dt)) ?>">
                                <i class="fa fa-clock-o"></i> <span id="clock-expiration"><i class="fa fa-spinner fa-spin"></i></span></span>
                    <?php endif ?>
                <?php endif ?>

                <?php if ($lead->l_is_test) : ?>
                    <span class="label status-label bg-red">TEST</span>
                <?php endif ?>

            </h2>
            <div class="page-header__general">
                <?php if (!$lead->isNewRecord) : ?>
                    <?php if ($lead->employee_id && $lead->employee) : ?>
                        <div class="page-header__general-item" title="Assigned to:">
                            <i class="fa fa-user"></i> <?= Html::encode($lead->employee->username) ?>
                        </div>
                    <?php endif; ?>
                    <div class="page-header__general-item">
                        <strong>Client:</strong>
                        <?= \src\formatters\client\ClientTimeFormatter::format($lead->getClientTime2(), $lead->offset_gmt); ?>
                    </div>

                    <div class="page-header__general-item">
                        <strong>UID:</strong>
                        <span><?= Html::encode($lead->uid)?></span>
                        <?php //= Html::a($lead->uid, '#', ['id' => 'view-flow-transition']) ?>
                    </div>

                    <?php
                        /** @abac new LeadAbacDto($lead, Auth::id()), LeadAbacObject::UI_DISPLAY_MARKETING_SOURCE, LeadAbacObject::ACTION_READ, Access to show Marketing source */
                        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
                    ?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_DISPLAY_MARKETING_SOURCE, LeadAbacObject::ACTION_READ)) : ?>
                        <div class="page-header__general-item">
                            <?php $typeCreate = Lead::TYPE_CREATE_LIST[$lead->l_type_create] ?? '-' ?>
                            <strong title="<?php echo $typeCreate?>">Market:</strong>
                            <span>
                                <?= ($lead->project ? $lead->project->name : '') .
                                    ($lead->source ? ' - <span title="' . $lead->source->id . '/' . $lead->source->cid . '">' . $lead->source->name . '</span>' : '')
                                ?>
                            </span>
                        </div>
                    <?php endif ?>

                    <?php if (Yii::$app->user->can('lead/view_HybridUid_View', ['lead' => $lead])) : ?>
                        <div class="page-header__general-item">
                            <strong title="Hybrid UID">Booking ID:</strong>
                            <span><?= Html::encode($lead->hybrid_uid)?></span>
                        </div>
                    <?php endif ?>

                    <div class="page-header__general-item">
                        <strong>PNR:</strong>
                        <span>
                            <?= implode('&#9900', $lead->getAdditionalInformationMultiplePnr()) ?>
                        </span>
                    </div>
                    <?php if ($lead->l_type) : ?>
                        <div class="page-header__general-item">
                            <strong>Type:</strong>
                            <span><?= Html::encode($lead::TYPE_LIST[$lead->l_type])?></span>
                        </div>
                    <?php endif ?>

                    <?php if ($lead->hasObjectTasks()) : ?>
                        <div class="page-header__general-item" data-toggle="tooltip" data-placement="top" title="Auto Follow Up jobs">
                            <strong><i class="fa fa-tasks" aria-hidden="true"></i></strong>
                            <span>
                            <?= $lead->countObjectTask() ?>
                        </span>
                        </div>
                    <?php endif; ?>

<!--                    <div class="page-header__general-item">-->
<!--                        --><?php //= $this->render('_rating', [
//                            'lead' => $lead
//                        ]) ?>
<!--                    </div>-->
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php

if ($lead->l_expiration_dt && !LeadHelper::expiredLead($lead)) :
    $leftTime = LeadHelper::expirationNowDiffInSeconds($lead->l_expiration_dt);
    $js = <<<JS
        let leftTime = '{$leftTime}';
        $('#clock-expiration')
            .timer('remove')
            .timer({
                format: '%Dd %Hh %Mm %Ss', 
                duration: leftTime, 
                countdown: true,
                callback: function() {
                    $('#clock-expiration').timer('remove').remove();
                    $('.box-clock-expiration').removeClass('bg-orange').addClass('bg-red').html('<i class="fa fa-clock-o"></i> Expired</span>');
                }
            }).timer('start');
JS;
    $this->registerJs($js);
endif;

$jsTimerLPP = <<<JS
  $('.enable-timer-lpp').each( function (i, e) {
      let seconds = $(e).attr('data-seconds');
      if (seconds < 0) {
          var params = {format: '%d %H:%M:%S', seconds: Math.abs(seconds)};
      } else {
          var params = {format: '%d %H:%M:%S', countdown: true, duration: seconds + 's', callback: function () {
              $(e).timer('remove').timer({format: '%d %H:%M:%S', seconds: 0}).timer('start');
              
              $(e).closest('span.label.label-warning')
                  .removeClass('label-warning')
                  .addClass('label-danger');
          }};
      }
      $(e).timer(params).timer('start');
  });
JS;

if (LeadHelper::isShowLppTimer($lead) || LeadBusinessExtraQueueService::ffIsEnabled() === true) {
    $this->registerJs($jsTimerLPP, \yii\web\View::POS_READY);
}
