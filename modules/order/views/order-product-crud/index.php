<?php

use modules\order\src\grid\columns\OrderColumn;
use modules\product\src\grid\columns\ProductQuoteColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\order\src\entities\orderProduct\search\OrderProductCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => OrderColumn::class,
                'attribute' => 'orp_order_id',
                'relation' => 'orpOrder',
            ],
            [
                'class' => ProductQuoteColumn::class,
                'attribute' => 'orp_product_quote_id',
                'relation' => 'orpProductQuote',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'orp_created_user_id',
                'relation' => 'orpCreatedUser'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'orp_created_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
