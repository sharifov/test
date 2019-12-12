<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\OfferProductSearch */
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
            ['class' => 'yii\grid\SerialColumn'],

            'op_offer_id',
            'op_product_quote_id',
            'op_created_user_id',
            //'op_created_dt',
            [
                'attribute' => 'op_created_dt',
                'value' => static function(\common\models\OfferProduct $model) {
                    return $model->op_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->op_created_dt)) : '-';
                },
                'format' => 'raw',
            ],



            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
