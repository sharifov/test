<?php

use modules\product\src\grid\columns\ProductOptionPriceTypeColumn;
use modules\product\src\grid\columns\ProductTypeColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\product\src\entities\productOption\search\ProductOptionCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Options';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-option-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Option', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'po_id',
            'po_key',
            [
                'class' => ProductTypeColumn::class,
                'attribute' => 'po_product_type_id',
                'onlyEnabled' => true,
            ],
            'po_name',
            [
                'class' => ProductOptionPriceTypeColumn::class,
                'attribute' => 'po_price_type_id',
            ],
            'po_max_price',
            'po_min_price',
            'po_price',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'po_enabled',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'po_created_user_id',
                'relation' => 'poCreatedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'po_updated_user_id',
                'relation' => 'poUpdatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'po_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'po_updated_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
