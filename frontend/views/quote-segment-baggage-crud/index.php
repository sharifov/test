<?php

use common\models\QuoteSegmentBaggage;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuoteSegmentBaggageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Segment Baggages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-baggage-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'qsb_pax_code',
            'qsb_segment_id',
            'qsb_airline_code',
            'qsb_allow_pieces',
            'qsb_allow_weight',
            'qsb_allow_unit',
            'qsb_allow_max_weight',
            'qsb_allow_max_size',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuoteSegmentBaggage $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'qsb_id' => $model->qsb_id]);
                }
            ],
        ],
    ]); ?>


</div>
