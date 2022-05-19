<?php

/**
 * @var ActiveDataProvider $dataProviderPendingRequests
 * @var ShiftScheduleRequestSearch $searchModelPendingRequests
 * @var View $this
 */

use common\components\grid\DateTimeColumn;
use common\models\Lead;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

?>

<?php Pjax::begin([
    'id' => 'pjax-schedule-pending-request',
    'enablePushState' => false,
    'enableReplaceState' => false,
]); ?>

<div class="x_panel">
    <div class="x_title">
        <h2>
            <i class="fa fa-bars"></i> Pending Schedule Request List
        </h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <?= GridView::widget([
            'id' => 'gridview-pending-requests',
            'dataProvider' => $dataProviderPendingRequests,
            'filterModel' => $searchModelPendingRequests,
            'filterUrl' => Url::to(['shift-schedule/schedule-pending-requests']),
            'options' => ['data-pjax' => true ],
            'columns' => [
                [
                    'attribute' => 'ssr_id',
                    'options' => [
                        'style' => 'width: 55px',
                    ],
                    'label' => 'Id'
                ],
                [
                    'label' => 'Type',
                    'value' => static function (ShiftScheduleRequestSearch $model) {
                        return $model->srhSst ? $model->srhSst->getColorLabel() : '-';
                    },
                    'format' => 'raw',

                ],
                [
                    'attribute' => 'ssr_sst_id',
                    'value' => function (ShiftScheduleRequestSearch $model) {
                        return Html::a(
                            $model->getScheduleTypeTitle() ?? $model->ssr_sst_id,
                            null,
                            ['class' => 'btn-open-timeline-pending', 'data-tl_id' => $model->ssr_id]
                        );
                    },
                    'options' => [
                        'style' => 'width: 20%',
                    ],
                    'label' => 'Schedule Type',
                    'filter' => UserShiftScheduleHelper::getAvailableScheduleTypeList(),
                    'format' => 'raw',
                ],
                [
                    'label' => 'Start Date Time',
                    'class' => DateTimeColumn::class,
                    'value' => function (ShiftScheduleRequestSearch $model) {
                        return $model->srhUss->uss_start_utc_dt ?? '';
                    },
                    'format' => 'byUserDateTime',
                    'filter' => false,
                ],
                [
                    'label' => 'Duration',
                    'value' => function (ShiftScheduleRequestSearch $model) {
                        return Lead::diffFormat((new DateTime($model->srhUss->uss_start_utc_dt ?? ''))->diff(new DateTime($model->srhUss->uss_end_utc_dt ?? '')));
                    },
                ],
                [
                    'label' => 'End Date Time',
                    'class' => DateTimeColumn::class,
                    'value' => function (ShiftScheduleRequestSearch $model) {
                        return $model->srhUss->uss_end_utc_dt ?? '';
                    },
                    'format' => 'byUserDateTime',
                    'filter' => false
                ],
                [
                    'attribute' => 'ssr_created_dt',
                    'label' => 'Pending Time',
                    'value' => static function (ShiftScheduleRequestSearch $model) {
                        return Yii::$app->formatter->asRelativeDt($model->ssr_created_dt);
                    },
                    'options' => [
                        'style' => 'width:180px'
                    ],
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'filter' => false,
                ],
                [
                    'label' => 'Status',
                    'attribute' => 'ssr_status_id',
                    'value' => function (ShiftScheduleRequestSearch $model) {
                        $statusName = $model->getStatusName();
                        if (!empty($statusName)) {
                            return sprintf(
                                '<span class="badge badge-%s">%s</span>',
                                $model->getStatusNameColor(),
                                $statusName
                            );
                        }
                        return $model->ssr_status_id;
                    },
                    'options' => [
                        'style' => 'width: 10%',
                    ],
                    'filter' => false,
                    'format' => 'raw',
                ],
                [
                    'label' => '',
                    'value' => function (ShiftScheduleRequestSearch $model) {
                        return Html::a(
                            '<i class="fas fa-eye"></i> &nbsp;',
                            null,
                            ['class' => 'btn-open-timeline-pending', 'data-tl_id' => $model->ssr_id]
                        );
                    },
                    'format' => 'raw',
                ]
            ],
        ]) ?>
    </div>
</div>
<?php Pjax::end(); ?>

<?php
$openModalPendingEventUrl = Url::to(['shift/user-shift-schedule-request/get-event']);
$js = <<<JS
    var openModalPendingEventUrl = '$openModalPendingEventUrl';
    function openModalPendingEventId(id)
    {
        let modal = $('#modal-md');
        let eventUrl = openModalPendingEventUrl + '?id=' + id;
        $('#modal-md-label').html('Schedule Event: ' + id);
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(eventUrl, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal('show');
            }
        });
    }
    $(document)
        .off('click', '.btn-open-timeline-pending')
        .on('click', '.btn-open-timeline-pending', function (e) {
            e.preventDefault();
            let id = $(this).data('tl_id');
            openModalPendingEventId(id);
        });
JS;

$this->registerJs($js);
