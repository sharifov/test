<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteRefund\ProductQuoteChangeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Changes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-change-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Change', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pqc_id',
            'pqc_pq_id',
            'pqc_case_id',
            'pqc_decision_user',
            'pqc_status_id',
            'pqc_decision_type_id',
            [
                'attribute' => 'pqc_created_dt',
                'class' => DateTimeColumn::class
            ],
            [
                'attribute' => 'pqc_updated_dt',
                'class' => DateTimeColumn::class
            ],
            [
                'attribute' => 'pqc_decision_dt',
                'class' => DateTimeColumn::class
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
