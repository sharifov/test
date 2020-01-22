<?php

use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProductTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'pt_id',
            'pt_key',
            'pt_name',
            'pt_service_fee_percent',
            'pt_description:ntext',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'pt_enabled',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pt_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pt_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
