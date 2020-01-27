<?php

use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
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
            'op_offer_id',
            'op_product_quote_id',
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
