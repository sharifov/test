<?php

/**
 * @var View $this
 * @var ShiftScheduleRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use common\components\grid\DateTimeColumn;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\widgets\UserSelect2Widget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = 'Shift Schedule Requests';
$this->params['breadcrumbs'][] = $this->title;

?>
<?php Pjax::begin([
    'id' => 'pjax-all-requests-history',
    'enablePushState' => false,
    'enableReplaceState' => false,
]); ?>

<div class="shift-schedule-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ssr_id',
                'value' => function (ShiftScheduleRequest $model) {
                    return Html::tag('span', $model->ssr_id, [
                        'data-toggle' => 'tooltip',
                        'data-html' => 'true',
                        'data-original-title' => sprintf(
                            'Schedule Request Id: %s<br> Schedule Event Id: %s',
                            $model->ssr_id,
                            $model->ssr_uss_id
                        ),
                        'style' => 'border-bottom: 1px dotted #000; cursor: help;',
                    ]);
                },
                'options' => [
                    'style' => 'width: 5%',
                ],
                'label' => 'Id',
                'format' => 'raw',
            ],
//            [
//                'attribute' => 'ssr_uss_id',
//                'options' => [
//                    'style' => 'width: 10%',
//                ],
//            ],
            [
                'attribute' => 'ssr_sst_id',
                'value' => function (ShiftScheduleRequest $model) {
                    return Html::a(
                        $model->getScheduleTypeTitle() ?? $model->ssr_sst_id,
                        null,
                        ['class' => 'btn-open-timeline', 'data-tl_id' => $model->ssr_id, 'data-uss_id' => $model->ssr_uss_id]
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
                'label' => 'Status',
                'attribute' => 'ssr_status_id',
                'value' => function (ShiftScheduleRequest $model) {
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
                'filter' => ShiftScheduleRequest::getStatusList(),
                'format' => 'raw',
            ],
            [
                'attribute' => 'ssr_description',
                'options' => [
                    'style' => 'width: 20%',
                ],
            ],
            [
                'attribute' => 'ssr_created_dt',
                'label' => 'Created',
                'class' => DateTimeColumn::class,
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssr_created_dt ?? '';
                },
                'format' => 'byUserDateTime',
            ],
//            'ssr_updated_dt',
            [
                'attribute' => 'ssr_created_user_id',
                'options' => [

                ],
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssrCreatedUser->username ?? $model->ssr_created_user_id;
                },
                'label' => 'User create request',
                'filter' => UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'ssr_created_user_id'
                ]),
            ],
            [
                'attribute' => 'ssr_updated_user_id',
                'options' => [

                ],
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssrUpdatedUser->username ?? $model->ssr_updated_user_id;
                },
                'label' => 'User make decision',
                'filter' => UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'ssr_updated_user_id'
                ]),
            ],
            [
                'class' => \yii\grid\ActionColumn::class,
                'template' => '{history}',
                'buttons' => [
                    'history' => function ($url, ShiftScheduleRequest $model) {
                        return Html::a(
                            '<i class="fa fa-th-list"></i>',
                            [
                                'shift-schedule-request/get-history',
                                'id' => $model->ssr_id,
                            ],
                            [
                                'title' => 'Request history',
                                'class' => 'show-history',
                                'data' => [
                                    'pjax' => false,
                                ],
                            ]
                        );
                    }
                ],
            ],
        ],
    ]); ?>

</div>

<?php
$openModalEventUrl = Url::to(['shift-schedule-request/get-event']);
$js = <<<JS
    var openModalEventUrl = '$openModalEventUrl';
    var modal = $('#modal-md');
    $('.show-history').on('click', function (e) {
        e.preventDefault();
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('History for selected schedule request');
        modal.modal('show');
        modal.find('.modal-body')
             .load($(this).attr('href'));
    });

    $(document).on('pjax:end', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    
    function openModalEventId(id, ussId)
    {
        let modal = $('#modal-md');
        let eventUrl = openModalEventUrl + '?id=' + id;
        $('#modal-md-label').html('Schedule Event: ' + (ussId || id)); // todo: add ssr_sst_id
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(eventUrl, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal('show');
            }
        });
    }
    
    $('body').off('click', '.btn-open-timeline').on('click', '.btn-open-timeline', function (e) {
        e.preventDefault();
        let id = $(this).data('tl_id');
        let ussId = $(this).data('uss_id');
        openModalEventId(id, ussId);
    });
JS;
$this->registerJs($js);

Pjax::end();
