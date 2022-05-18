<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProviderAll
 * @var ShiftScheduleRequestSearch $searchModelAll
 */

use common\components\grid\DateTimeColumn;
use common\models\Lead;
use dosamigos\datepicker\DatePicker;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\widgets\UserSelect2Widget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

<div class="row">
    <div class="col-md-12">
        <div class="x_title">
            <h2>
                <i class="fa fa-bars"></i> TimeLine Schedule All Requests
            </h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?= GridView::widget([
                'id' => 'gridview-all-requests',
                'dataProvider' => $dataProviderAll,
                'filterModel' => $searchModelAll,
                'filterUrl' => Url::to(['user-shift-schedule-request/schedule-all-requests']),
                'options' => ['data-pjax' => true],
                'columns' => [
                    [
                        'attribute' => 'ssr_id',
                        'options' => [
                            'style' => 'width: 55px',
                        ],
                        'label' => 'Id'
                    ],
//                    [
//                        'label' => 'Type',
//                        'value' => static function (ShiftScheduleRequestSearch $model) {
//                            return $model->srhSst ? $model->srhSst->getColorLabel() : '-';
//                        },
//                        'format' => 'raw',
//                    ],
                    [
                        'attribute' => 'ssr_sst_id',
                        'value' => function (ShiftScheduleRequestSearch $model) {
                            return Html::a(
                                $model->getScheduleTypeTitle() ?? $model->ssr_sst_id,
                                null,
                                ['class' => 'btn-open-timeline', 'data-tl_id' => $model->ssr_id]
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
                        'filter' => false
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
//                    [
//                        'attribute' => 'ssr_created_dt',
//                        'label' => 'Request created',
//                        'value' => static function (ShiftScheduleRequestSearch $model) {
//                            return $model->ssr_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ssr_created_dt)) : '-';
//                        },
//                        'format' => 'raw',
//                        'filter' => DatePicker::widget([
//                            'model' => $searchModelAll,
//                            'attribute' => 'ssr_created_dt',
//                            'clientOptions' => [
//                                'autoclose' => true,
//                                'format' => 'yyyy-mm-dd',
//                            ],
//                            'options' => [
//                                'autocomplete' => 'off',
//                                'placeholder' => 'Choose Date'
//                            ],
//                        ]),
//                    ],
                    [
                        'attribute' => 'ssr_created_user_id',
                        'options' => [

                        ],
                        'value' => function (ShiftScheduleRequestSearch $model) {
                            return $model->ssrCreatedUser->nickname ?? $model->ssr_created_user_id;
                        },
                        'label' => 'User create request',
                        'filter' => UserSelect2Widget::widget([
                            'model' => $searchModelAll,
                            'attribute' => 'ssr_created_user_id'
                        ]),
                    ],
                    [
                        'attribute' => 'ssr_updated_user_id',
                        'options' => [

                        ],
                        'value' => function (ShiftScheduleRequestSearch $model) {
                            return $model->ssrUpdatedUser->nickname ?? $model->ssr_updated_user_id;
                        },
                        'label' => 'User make decision',
                        'filter' => UserSelect2Widget::widget([
                            'model' => $searchModelAll,
                            'attribute' => 'ssr_updated_user_id'
                        ]),
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
                        'filter' => ShiftScheduleRequest::getList(),
                        'format' => 'raw',
                    ],
                    'ssr_description',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{history}',
                        'buttons' => [
                            'history' => function ($url, ShiftScheduleRequestSearch $model) {
                                return Html::a(
                                    '<i class="glyphicon glyphicon-time"></i>',
                                    [
                                        'user-shift-schedule-request/get-history',
                                        'id' => $model->ssr_uss_id,
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
            ]) ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    var modal = $('#modal-md');
    $('.show-history').on('click', function (e) {
        e.preventDefault();
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('History for selected schedule request');
        modal.modal('show');
        modal.find('.modal-body')
             .load($(this).attr('href'));
    });
    
JS;
$this->registerJs($js);
