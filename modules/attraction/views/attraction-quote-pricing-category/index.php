<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\attraction\models\search\AttractionQuotePricingCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attraction Quote Pricing Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-quote-pricing-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Attraction Quote Pricing Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'atqpc_id',
            'atqpc_attraction_quote_id',
            'atqpc_category_id',
            'atqpc_label',
            'atqpc_min_age',
            'atqpc_max_age',
            'atqpc_min_participants',
            'atqpc_max_participants',
            'atqpc_quantity',
            'atqpc_price',
            'atqpc_currency',
            'atqpc_system_mark_up',
            'atqpc_agent_mark_up',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
