<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use sales\helpers\call\CallHelper;
use sales\model\voiceMailRecord\entity\VoiceMailRecord;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\voiceMailRecord\entity\search\VoiceMailRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Voice Mail Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voice-mail-record-list">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'voice-mail-pjax']); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'vmr_call_id',
                [
                    'label' => 'Recording',
                    'value' => static function (VoiceMailRecord $model) {
                        {
                        if (!$model->vmr_record_sid) {
                            return '-';
                        }

                        if ($model->vmr_duration && $model->vmr_duration >= 3600) {
                            $format = 'H:i:s';
                        } else {
                            $format = 'i:s';
                        }

                            return  \yii\helpers\Html::button(gmdate($format, $model->vmr_duration) . ' <i class="fa fa-volume-up"></i>', ['title' => $model->vmr_duration . ' (sec)', 'class' => 'btn btn-' . ($model->vmr_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url' . ($model->vmr_new ? ' btn-voice-mail-showed' : ''), 'data-call-id' => $model->vmr_call_id, 'data-source_src' => $model->getRecordingUrl()]);
                        }
                    },
                    'format' => 'raw',

                ],
                'vmr_client_id:client',
                ['class' => DateTimeColumn::class, 'attribute' => 'vmr_created_dt'],
                'vmr_duration',
                ['class' => BooleanColumn::class, 'attribute' => 'vmr_new'],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [

                        'delete' => static function ($url, VoiceMailRecord $model) {
                            return Html::a('<i class="glyphicon glyphicon-trash"></i>', ['/voice-mail-record/remove', 'id' => $model->vmr_call_id], [
                                'title' => 'View',
                                'data-pjax' => 0,
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php
$url = Url::to(['/voice-mail-record/showed']);
$js = <<<JS
$(document).on('click', '.btn-voice-mail-showed', function() {
     let callId = $(this).attr('data-call-id');
     $.ajax({
            type: 'post',
            url: '{$url}',
            dataType: 'json',
            data: {
                callId: callId
            }
        })
            .done(function (data) {
                if (data.error) {
                    createNotify('Mark showed error', data.message, 'error');
                    return;
                }
            })
            .fail(function () {
                createNotify('Mark showed error', 'Server error', 'error');
            })
});
JS;
$this->registerJs($js);