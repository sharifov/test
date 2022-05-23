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
use yii\web\View;

$this->title = 'Shift Schedule Requests';
$this->params['breadcrumbs'][] = $this->title;

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
                        ['/user-shift-schedule-crud/view', 'id' => $model->ssr_uss_id],
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
                    return $model->ssrCreatedUser->nickname ?? $model->ssr_created_user_id;
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
                    return $model->ssrUpdatedUser->nickname ?? $model->ssr_updated_user_id;
                },
                'label' => 'User make decision',
                'filter' => UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'ssr_updated_user_id'
                ]),
            ],
        ],
    ]); ?>


</div>
