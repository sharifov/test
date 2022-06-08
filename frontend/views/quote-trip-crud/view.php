<?php

use common\models\QuoteSegment;
use common\models\QuoteTrip;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteTrip */
/* @var $searchModel common\models\search\QuoteSegmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->qt_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Trips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-trip-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?php //Html::a('Update', ['update', 'qt_id' => $model->qt_id], ['class' => 'btn btn-primary']) ?>
<!--        --><?php //Html::a('Delete', ['delete', 'qt_id' => $model->qt_id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <div class="row">
        <div class="col-md-5">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'qt_id',
                    'qt_duration',
                    'qt_key',
                    'qt_quote_id',
                ],
            ]) ?>
        </div>
        <div class="col-md-7">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{errors}\n{items}\n{pager}",
                'columns' => [
                    [
                        'attribute' => 'qs_id',
                        'format' => 'raw',
                        'value' => function (QuoteSegment $model) {
                            return '<i class="fa fa-link"></i> ' . Html::a($model->qs_id, ['/quote-segment-crud/index', 'QuoteSegmentSearch[qs_id]' => $model->qs_id], ['title' => 'Show', 'target' => '_blank', 'data-pjax' => 0]);
                        }
                    ],
                    'qs_departure_time',
                    'qs_arrival_time',
                    'qs_flight_number',
                    'qs_departure_airport_code',
                    'qs_arrival_airport_code',
                    [
                        'class' => ActionColumn::class,
                        'urlCreator' => function ($action, QuoteSegment $model2, $key, $index, $column) {
                            return Url::to(['quote-segment-crud/' . $action, 'qs_id' => $model2->qs_id]);
                        },
                        'template' => '{view}',
                    ]
                ]
            ]); ?>
        </div>
    </div>

</div>
