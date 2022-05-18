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
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

?>
<div class="shift-schedule-request-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'columns' => [
            [
                'attribute' => 'ssr_id',
                'options' => [
                    'style' => 'width: 5%',
                ],
                'label' => 'Id',
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
            ],
            [
                'attribute' => 'ssr_created_user_id',
                'options' => [

                ],
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssrCreatedUser->nickname ?? $model->ssr_created_user_id;
                },
                'label' => 'User create request',
            ],
            [
                'attribute' => 'ssr_updated_user_id',
                'options' => [

                ],
                'value' => function (ShiftScheduleRequest $model) {
                    return $model->ssrUpdatedUser->nickname ?? $model->ssr_updated_user_id;
                },
                'label' => 'User make decision',
            ],
        ],
    ]); ?>


</div>
