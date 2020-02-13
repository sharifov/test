<?php

use modules\product\src\grid\columns\ProductOptionColumn;
use modules\product\src\grid\columns\ProductQuoteColumn;
use modules\product\src\grid\columns\ProductQuoteOptionStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\product\src\entities\productQuoteOption\search\ProductQuoteOptionCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Options';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-option-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Option', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pqo_id',
            [
                'class' => ProductQuoteColumn::class,
                'attribute' => 'pqo_product_quote_id',
                'relation' => 'pqoProductQuote',
            ],
            [
                'class' => ProductOptionColumn::class,
                'attribute' => 'pqo_product_option_id',
                'relation' => 'pqoProductOption',
            ],
            'pqo_name',
            'pqo_description:ntext',
            [
                'class' => ProductQuoteOptionStatusColumn::class,
                'attribute' => 'pqo_status_id',
            ],
            'pqo_price',
            'pqo_client_price',
            'pqo_extra_markup',
            [
                'class' => UserColumn::class,
                'attribute' => 'pqo_created_user_id',
                'relation' => 'pqoCreatedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'pqo_updated_user_id',
                'relation' => 'pqoUpdatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqo_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqo_updated_dt',
            ],

            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
