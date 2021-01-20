<?php

use sales\helpers\call\CallHelper;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\client\helpers\ClientFormatter;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model CallLog */

$this->title = 'Call Id: ' . $model->cl_id;

?>
<div class="ajax-call-log-view">

    <h2>
        <i class="fa fa-phone-square"> </i> <?= Html::encode($this->title) ?>
        <?= CallLogStatus::asFormat($model->cl_status_id) ?>
    </h2>

    <?php if ($model->record) : ?>
        <?= CallHelper::displayAudioTag($model->recordingUrl, $model->cl_call_sid, ['style' => 'width: 100%;']) ?>
    <?php endif; ?>

    <div class="col-md-6">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'cl_id',
                'user:userNickname',
                'cl_status_id:callLogStatus',
                'cl_type_id:callLogType',
                'cl_category_id:callLogCategory',
                'cl_duration',
                'cl_phone_from:phoneOrNickname:From',
                'cl_phone_to:phoneOrNickname:To',
                'cl_call_created_dt:byUserDateTime',
                'cl_call_finished_dt:byUserDateTime',
            ],
        ]) ?>
    </div>

    <div class="col-md-6">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'cl_project_id',
                    'value' => static function (CallLog $model) {
                        if (!$model->cl_project_id) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->project, 'projectName');
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'cl_department_id',
                    'value' => static function (CallLog $model) {
                        if (!$model->cl_department_id) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->department, 'departmentName');
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'cl_client_id',
                    'value' => static function (CallLog $model) {
                        if (!$model->cl_client_id) {
                            return '';
                        }
                        $clientName = $model->client->getShortName() . ' (' . $model->cl_client_id . ')';
                        if ($model->client->isExcluded()) {
                            $clientName = ClientFormatter::formatExclude($model->client) . $clientName;
                        }
                        return  $clientName;
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Lead',
                    'value' => static function (CallLog $model) {
                        if (!$model->lead) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->lead, 'lead');
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Case',
                    'value' => static function (CallLog $model) {
                        if (!$model->case) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->case, 'case');
                    },
                    'format' => 'raw'
                ],
            ],
        ]) ?>
    </div>


</div>
