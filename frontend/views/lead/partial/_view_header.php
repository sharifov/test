<?php
/**
 * @var $this \yii\web\View
 * @var $title string
 * @var $lead Lead
 */

use common\models\Lead;
use yii\helpers\Html;

?>
<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title">
                <?= Html::encode($title) ?>
                <?php
                    if($lead->clone_id && $cloneLead = $lead->clone) {
                        echo Html::a('(Cloned from ' . $lead->clone_id . ' )', ['lead/view', 'gid' => $cloneLead->gid], ['title' => 'Clone reason: ' . $lead->description]);
                    }
                ?>
                <?= $lead->getStatusLabel() ?>
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
                        <strong>Market:</strong>
                        <span><?= Html::encode(($lead->project ? $lead->project->name : '') . ($lead->source ? ' - ' . $lead->source->name : ''))?></span>
                    </div>

                    <div class="page-header__general-item">
                        <strong>PNR:</strong>
                        <span>
                            <?= implode('&#9900', $lead->getAdditionalInformationMultiplePnr()) ?>
                        </span>
                    </div>

                    <div class="page-header__general-item">
                        <strong title="Hybrid UID">HUID:</strong>
                        <span><?= Html::encode($lead->hybrid_uid)?></span>
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