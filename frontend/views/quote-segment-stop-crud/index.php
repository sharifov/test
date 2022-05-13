<?php

use common\models\QuoteSegment;
use common\models\QuoteSegmentStop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuoteSegmentStopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Segment Stop';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-stop-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <!--    --><?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'qss_segment_id',
                'value' => static function (QuoteSegmentStop $model) {
                    if ($model->qss_segment_id) {
                        return Html::a($model->qss_segment_id, ['quote-segment-crud/view', 'qs_id' => $model->qss_segment_id], [
                            'data-pjax' => 0,
                            'target' => '_blank'
                        ]);
                    }
                    return $model->qss_segment_id;
                },
                'format' => 'raw'
            ],
            'qss_id',
            [
                'label' => 'Location Code',
                'attribute' => 'qss_location_code',
                'value' => static function (QuoteSegmentStop $model) {
                    return Html::tag('span', $model->qss_location_code, ['class' => 'label label-info']);
                },
                'format' => 'raw',
            ],
            [
                'class' => \common\components\grid\DateColumn::class,
                'label' => 'Departure Dt',
                'attribute' => 'qss_departure_dt',
            ],
            [
                'class' => \common\components\grid\DateColumn::class,
                'label' => 'Arrival Dt',
                'attribute' => 'qss_arrival_dt',
            ],
            'qss_duration',
            //'qss_elapsed_time:datetime',
            //'qss_equipment',
            //'qss_segment_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuoteSegmentStop $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'qss_id' => $model->qss_id]);
                },
            ],
        ],
    ]); ?>


</div>
