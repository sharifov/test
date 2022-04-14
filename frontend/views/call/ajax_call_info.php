<?php

use common\models\Call;
use src\auth\Auth;
use src\helpers\call\CallHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Call */

$this->title = 'Call Id: ' . $model->c_id;
//$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['list']];
//$this->params['breadcrumbs'][] = $this->title;
//\yii\web\YiiAsset::register($this);
?>
<div class="ajax-call-view">

    <h2>
        <i class="fa fa-phone-square"></i> <?= Html::encode($this->title) ?>
        <?=$model->getStatusLabel()?>
        <?php if (($model->isStatusRinging() || $model->isStatusInProgress()) && Auth::can('/call/cancel-manual')) : ?>
            <?= Html::button('Cancel Call', ['class' => 'btn btn-danger cancel-call-btn']) ?>
            <?php
            $callCancelUrl = Url::to(['/call/cancel-manual']);
            $callId = $model->c_id;
            $cancelStatus = Call::STATUS_CANCELED;

            $js = <<<JS
$('.cancel-call-btn').click(function (e) {
   if (confirm('The call will be canceled. Proceed?')) {
        $.ajax({
            type: 'post',
            url: '{$callCancelUrl}',
            data: {id: {$callId}}
        })
        .done(function(data) {
            $('#call-box-modal').modal('toggle');
            obj = new Object();
            obj.id = {$callId};
            obj.status = {$cancelStatus};
            if (typeof PhoneWidget === 'object') {
                PhoneWidget.refreshCallStatus(obj);
            }
            if (data.success) {
                createNotifyByObject({title: "Call status", type: "success", text: 'Success', hide: true});
            } else {
               let text = 'Error. Try again later';
               if (data.message) {
                   text = data.message;
               }
               createNotifyByObject({title: "Call status", type: "error", text: text, hide: true});
            }
        })
        .fail(function() {
            createNotifyByObject({title: "Call status", type: "error", text: 'Try again later.', hide: true});
        })
   }
});
JS;
            $this->registerJs($js);

            ?>
        <?php endif;?>
    </h2>


    <?php if ($model->recordingUrl) :?>
        <?= CallHelper::displayAudioTag($model->recordingUrl, ['style' => 'width: 100%']) ?>
    <?php endif;?>

    <div class="col-md-6">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'c_id',
                'cCreatedUser:userNickname:User',
                'statusName:ntext:Status',
                'callTypeName:ntext:Type',
                'sourceName:ntext:Category',
                'c_call_duration:ntext:Duration',
                'c_from:phoneOrNickname',
                'c_to:phoneOrNickname',
                'c_created_dt:byUserDateTime',
                'c_updated_dt:byUserDateTime',
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Project',
                    'attribute' => 'c_project_id',
                    'value' => static function (Call $model) {
                        if (!$model->c_project_id) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->cProject, 'projectName');
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Department',
                    'attribute' => 'c_dep_id',
                    'value' => static function (Call $model) {
                        if (!$model->c_dep_id) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->cDep, 'departmentName');
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'c_client_id',
                    'value' => static function (Call $model) {
                        if (!$model->c_client_id) {
                            return '';
                        }
                        return $model->cClient->getShortName() . ' (' . $model->c_client_id . ')';
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Lead',
                    'value' => static function (Call $model) {
                        if (!$model->cLead) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->cLead, 'lead');
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Case',
                    'value' => static function (Call $model) {
                        if (!$model->cCase) {
                            return '';
                        }
                        return Yii::$app->formatter->format($model->cCase, 'case');
                    },
                    'format' => 'raw'
                ],
            ],
        ]) ?>
    </div>

</div>
