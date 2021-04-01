<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteRelation\search\ProductQuoteRelationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Relations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-relation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Relation', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-product-quote-relation']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'class' => modules\product\src\grid\columns\ProductQuoteColumn::class,
                'attribute' => 'pqr_parent_pq_id',
                'relation' => 'pqrParentPq',
            ],
            [
                'class' => modules\product\src\grid\columns\ProductQuoteColumn::class,
                'attribute' => 'pqr_related_pq_id',
                'relation' => 'pqrRelatedPq',
            ],
            [
                'attribute' => 'pqr_type_id',
                'label' => 'Project',
                'value' => static function (ProductQuoteRelation $model) {
                    return ProductQuoteRelation::getTypeName($model->pqr_type_id);
                },
                'filter' => ProductQuoteRelation::TYPE_LIST,
                'format' => 'raw',
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'pqrCreatedUser',
                'attribute' => 'pqr_created_user_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqr_created_dt',
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
