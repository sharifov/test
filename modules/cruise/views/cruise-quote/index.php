<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\cruise\src\entity\cruiseQuote\search\CruiseQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cruise Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cruise-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cruise Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-cruise-quote']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'crq_id',
            'crq_hash_key',
            'crq_product_quote_id',
            'crq_cruise_id',
            'crq_amount',
            'crq_amount_per_person',
            'crq_currency',
            'crq_adults',
            'crq_children',
            'crq_system_mark_up',
            'crq_agent_mark_up',
            'crq_service_fee_percent',


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
