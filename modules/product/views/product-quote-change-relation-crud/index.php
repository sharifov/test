<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Change Relations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-change-relation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Change Relation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-product-quote-change-relation']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'columns' => [
            ['class' => SerialColumn::class],

            'pqcr_pqc_id',
            'pqcr_pq_id',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
