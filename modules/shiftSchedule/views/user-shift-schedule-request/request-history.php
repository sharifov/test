<?php

/**
 * @var View $this
 * @var ShiftScheduleRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use common\components\grid\DateTimeColumn;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

?>
<?php Pjax::begin([
    'id' => 'pjax-shift-schedule-request',
    'enablePushState' => false,
    'enableReplaceState' => false,
]); ?>
<div class="shift-schedule-request-index">

    <?= GridView::widget([
        'id' => 'grid-view-shift-schedule-request',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ssr_uss_id',
                'label' => 'User Shift Schedule Id',
                'filter' => false,
            ],
            [
                'label' => 'Type',
                'value' => static function (ShiftScheduleRequest $model) {
                    return $model->srhSst ? $model->srhSst->getColorLabel() : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'ssr_sst_id',
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->getScheduleTypeTitle() ?? $model->ssr_sst_id;
                },
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
                'attribute' => 'ssr_created_dt',
                'label' => 'Created',
                'class' => DateTimeColumn::class,
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssr_created_dt ?? '';
                },
                'format' => 'byUserDateTime',
                'filter' => false,
            ],
            [
                'attribute' => 'ssr_created_user_id',
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssrCreatedUser->username ?? $model->ssr_created_user_id;
                },
                'label' => 'User create request',
                'filter' => false,
            ],
            [
                'attribute' => 'ssr_updated_user_id',
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssrUpdatedUser->username ?? $model->ssr_updated_user_id;
                },
                'label' => 'User make decision',
                'filter' => false,
            ],
        ],
    ]); ?>

</div>
<?php Pjax::end(); ?>
