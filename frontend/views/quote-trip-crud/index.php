<?php

use common\models\QuoteTrip;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuoteTripSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Trips';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-trip-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--    <p>-->
    <!--        --><?php //Html::a('Create Quote Trip', ['create'], ['class' => 'btn btn-success']) ?>
    <!--    </p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'qt_id',
            [
                'attribute' => 'qt_quote_id',
                'format' => 'raw',
                'value' => function (QuoteTrip $model) {
                    return '<i class="fa fa-link"></i> ' .
                        Html::a(
                            $model->qt_quote_id,
                            ['/quotes/index', 'QuoteSearch[id]' => $model->qt_quote_id],
                            ['title' => 'Show', 'target' => '_blank', 'data-pjax' => 0]
                        );
                }
            ],
            [
                'header' => 'Segment(s)',
                'format' => 'raw',
                'value' => function (QuoteTrip $model) {
                    return $model->getQuoteSegments()->count();
                }
            ],
            'qt_duration',
            'qt_key',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuoteTrip $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'qt_id' => $model->qt_id]);
                },
                'template' => '{view}',
            ],
        ],
    ]); ?>


</div>
