<?php

/**
 * @var $this \yii\web\View
 * @var $title string
 * @var $lead Lead
 */

use common\models\Lead;
use sales\helpers\lead\LeadHelper;
use yii\helpers\Html;

$bundle = \frontend\assets\TimerAsset::register($this);
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

                <?php if ($lead->l_expiration_dt) : ?>
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
                        <?= \sales\formatters\client\ClientTimeFormatter::format($lead->getClientTime2(), $lead->offset_gmt); ?>
                    </div>

                    <div class="page-header__general-item">
                        <strong>UID:</strong>
                        <span><?= Html::encode($lead->uid)?></span>
                        <?php //= Html::a($lead->uid, '#', ['id' => 'view-flow-transition']) ?>
                    </div>

                    <div class="page-header__general-item">
                        <?php $typeCreate = Lead::TYPE_CREATE_LIST[$lead->l_type_create] ?? '-' ?>
                        <strong title="<?php echo $typeCreate?>">Market:</strong>
                        <span>
                            <?= ($lead->project ? $lead->project->name : '') .
                                ($lead->source ? ' - <span title="' . $lead->source->id . '/' . $lead->source->cid . '">' . $lead->source->name . '</span>' : '')
                            ?>
                        </span>
                    </div>

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
    $leftTime = LeadHelper::expirationNowDiffInSeconds($lead);
    $js = <<<JS
        let leftTime = '{$leftTime}';
        $('#clock-expiration')
            .timer('remove')
            .timer({
                format: '%Hh %Mm %Ss', 
                duration: leftTime, 
                countdown: true,
                 callback: function() {
                    $('#clock-expiration').timer('remove');
                    $('#clock-expiration').remove();
                    $('.box-clock-expiration').removeClass('bg-orange').addClass('bg-red').html('<i class="fa fa-clock-o"></i> Expired</span>');
                }
            }).timer('start');
JS;
    $this->registerJs($js);
endif;
