<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Labels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-label-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Label', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-quote-label', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'fql_quote_id',
            'fql_label_key',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
