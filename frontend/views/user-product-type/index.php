<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserProductTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Product Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-product-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Product Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'upt_user_id',
            'upt_product_type_id',
            'upt_commission_percent',
            'upt_product_enabled',
            'upt_created_user_id',
            //'upt_updated_user_id',
            //'upt_created_dt',
            //'upt_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
