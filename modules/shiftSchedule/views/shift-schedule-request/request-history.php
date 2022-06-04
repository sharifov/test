<?php

/**
 * @var View $this
 * @var ShiftScheduleRequestHistory $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

$view = $this;

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
                'attribute' => 'ssrh_id',
                'label' => 'Id',
                'filter' => false,
            ],
            [
                'header' => 'Who made the changes',
                'attribute' => 'ssrh_created_user_id',
                'value' => static function (ShiftScheduleRequestHistory $model) {
                    $template = '';
                    if ($model->ssrh_created_user_id) {
                        $template .= '<i class="fa fa-user"></i> ';
                        $template .= Html::encode($model->whoCreated->username);
                    }
                    $template .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ssrh_created_dt));

                    return $template;
                },
                'format' => 'raw',
                'options' => [
                    'width' => '15%'
                ],
                'filter' => false,
            ],
            [
                'header' => 'Changed Attributes',
                'attribute' => 'ssrh_formatted_attr',
                'value' => static function (ShiftScheduleRequestHistory $model) use ($view) {
                    if ($model->ssrh_formatted_attr) {
                        return $view->render('partial/_formatted_attributes', [
                            'model' => $model
                        ]);
                    }

                    return '';
                },
                'filter' => false,
                'enableSorting' => false,
                'format' => 'raw',
                'options' => [
                    'width' => '100%'
                ]
            ],
        ],
    ]); ?>

</div>
<?php Pjax::end(); ?>
