<?php

/**
 * @var View $this
 * @var ShiftScheduleRequestSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

$this->title = 'Shift Schedule Requests';
$this->params['breadcrumbs'][] = $this->title;

$shiftScheduleTypes = ShiftScheduleType::getList(true);

?>
<div class="shift-schedule-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ssr_id',
                'options' => [
                    'style' => 'width: 5%',
                ],
            ],
            [
                'attribute' => 'ssr_uss_id',
                'options' => [
                    'style' => 'width: 10%',
                ],
            ],
            [
                'attribute' => 'ssr_sst_id',
                'value' => function (ShiftScheduleRequest $model) use ($shiftScheduleTypes) {
                    return $shiftScheduleTypes[$model->ssr_sst_id] ?? $model->ssr_sst_id;
                },
                'options' => [
                    'style' => 'width: 20%',
                ],
                'filter' => $shiftScheduleTypes,
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
                'filter' => ShiftScheduleRequest::getList(),
                'format' => 'raw',
            ],
            [
                'attribute' => 'ssr_description',
                'options' => [
                    'style' => 'width: 20%',
                ],
            ],
            'ssr_created_dt',
            'ssr_update_dt',
            'ssr_created_user_id',
            'ssr_updated_user_id',
        ],
    ]); ?>


</div>
