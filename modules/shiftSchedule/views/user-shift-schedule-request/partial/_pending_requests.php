<?php

/**
 * @var ActiveDataProvider $dataProvider
 * @var ShiftScheduleRequestSearch $searchModel
 */

use common\components\grid\DateTimeColumn;
use common\models\Lead;
use dosamigos\datepicker\DatePicker;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use src\widgets\UserSelect2Widget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="x_panel">
    <div class="x_title">
        <h2>
            <i class="fa fa-bars"></i> TimeLine Schedule Request List
        </h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <?= GridView::widget([
            'id' => 'gridview-pending-requests',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterUrl' => Url::to(['user-shift-schedule-request/schedule-pending-requests']),
            'options' => ['data-pjax' => true ],
            'columns' => [
                [
                    'attribute' => 'ssr_id',
                    'value' => function (ShiftScheduleRequestSearch $model) {
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
                        'style' => 'width: 55px',
                    ],
                    'label' => 'Id',
                    'format' => 'raw',
                ],
//                [
//                    'label' => 'Type',
//                    'value' => static function (ShiftScheduleRequestSearch $model) {
//                        return $model->srhSst ? $model->srhSst->getColorLabel() : '-';
//                    },
//                    'format' => 'raw',
//
//                ],
                [
                    'attribute' => 'ssr_sst_id',
                    'value' => function (ShiftScheduleRequestSearch $model) {
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
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'ssr_created_dt',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'clearBtn' => true,
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'placeholder' => 'Choose Date'
                        ],
                        'clientEvents' => [
                            'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                        ],
                    ]),
                ],
                [
                    'attribute' => 'ssr_created_user_id',
                    'options' => [
                        'style' => 'width: 200px',
                    ],
                    'value' => function (ShiftScheduleRequestSearch $model) {
                        return $model->ssrCreatedUser->username ?? $model->ssr_created_user_id;
                    },
                    'label' => 'User',
                    'filter' => UserSelect2Widget::widget([
                        'model' => $searchModel,
                        'attribute' => 'ssr_created_user_id'
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
                    'filter' => false,
                    'format' => 'raw',
                ],
            ],
        ]) ?>
    </div>
</div>
