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
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

?>
<?php Pjax::begin([
    'id' => 'pjax-pending-requests-history',
    'enablePushState' => false,
    'enableReplaceState' => false,
]); ?>
<div class="shift-schedule-request-index">

    <?= GridView::widget([
        'id' => 'pending-requests-history',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ssr_id',
                'options' => [
                    'style' => 'width: 5%',
                ],
                'label' => 'Id',
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
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->getScheduleTypeTitle() ?? $model->ssr_sst_id;
                },
                'options' => [
                    'style' => 'width: 20%',
                ],
                'label' => 'Schedule Type',
                'filter' => UserShiftScheduleHelper::getAvailableScheduleTypeList(),
            ],
            [
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
                'format' => 'raw',
                'label' => 'Status',
                'filter' => ShiftScheduleRequest::getStatusList(),
            ],
            [
                'attribute' => 'ssr_description',
                'options' => [
                    'style' => 'width: 20%',
                ],
                'label' => 'Description',
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
                'label' => 'End Date Time',
                'class' => DateTimeColumn::class,
                'value' => function (ShiftScheduleRequestSearch $model) {
                    return $model->srhUss->uss_end_utc_dt ?? '';
                },
                'format' => 'byUserDateTime',
                'filter' => false
            ],
//            [
//                'attribute' => 'ssr_created_dt',
//                'label' => 'Date',
//                'class' => DateTimeColumn::class,
//                'value' => function (ShiftScheduleRequest $model) {
//                    return $model->ssr_created_dt ?? '';
//                },
//                'format' => 'byUserDateTime',
//            ],
//            [
//                'attribute' => 'ssr_created_user_id',
//                'options' => [
//
//                ],
//                'value' => function (ShiftScheduleRequest $model) {
//                    return $model->ssrCreatedUser->nickname ?? $model->ssr_created_user_id;
//                },
//                'label' => 'User create request',
//            ],
//            [
//                'attribute' => 'ssr_updated_user_id',
//                'value' => function (ShiftScheduleRequest $model) {
//                    return $model->ssrUpdatedUser->username ?? $model->ssr_updated_user_id;
//                },
//                'filter' => false,
//                'label' => 'User make decision',
//            ],
        ],
    ]); ?>


</div>
<?php Pjax::end(); ?>