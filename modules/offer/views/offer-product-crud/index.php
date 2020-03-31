<?php

use modules\offer\src\grid\columns\OfferColumn;
use modules\product\src\grid\columns\ProductQuoteColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\offer\src\entities\offerProduct\search\OfferProductCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Offer Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Offer Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => OfferColumn::class,
                'attribute' => 'op_offer_id',
                'relation' => 'opOffer',
            ],
            [
                'class' => ProductQuoteColumn::class,
                'attribute' => 'op_product_quote_id',
                'relation' => 'opProductQuote',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'op_created_user_id',
                'relation' => 'opCreatedUser'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'op_created_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
