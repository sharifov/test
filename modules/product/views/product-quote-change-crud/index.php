<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteChange\search\ProductQuoteChangeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Changes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-change-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Change', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pqc_id',
            'pqc_pq_id',
            'pqc_case_id',
            'pqc_gid',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pqc_created_user_id',
                'relation' => 'pqcCreatedUser',
                'placeholder' => 'Created User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pqc_decision_user',
                'relation' => 'pqcDecisionUser',
                'placeholder' => 'Decision User'
            ],
            [
                'attribute' => 'pqc_status_id',
                'value' => static function (ProductQuoteChange $model) {
                    return $model->pqc_status_id ? ProductQuoteChangeStatus::asFormat($model->pqc_status_id) : $model->pqc_status_id;
                },
                'filter' => ProductQuoteChangeStatus::getList(),
                'format' => 'raw'
            ],
            [
                'attribute' => 'pqc_decision_type_id',
                'value' => static function (ProductQuoteChange $model) {
                    return $model->pqc_decision_type_id ? ProductQuoteChangeDecisionType::asFormat($model->pqc_decision_type_id) : $model->pqc_decision_type_id;
                },
                'filter' => ProductQuoteChangeDecisionType::getList(),
                'format' => 'raw'
            ],
            [
                'attribute' => 'pqc_type_id',
                'value' => static function (ProductQuoteChange $model) {
                    return $model->pqc_type_id ?
                        ProductQuoteChange::TYPE_LIST[$model->pqc_type_id] ?? 'Undefined' :
                        Yii::$app->formatter->nullDisplay;
                },
                'filter' => ProductQuoteChange::TYPE_LIST,
                'format' => 'raw',
            ],
            'pqc_is_automate:boolean',
            'pqc_refund_allowed:boolean',
            [
                'label' => 'ProductQuote Relations',
                'value' => static function (ProductQuoteChange $model) {
                    if (empty($model->productQuoteChangeRelations)) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    $result = '';
                    foreach ($model->productQuoteChangeRelations as $productQuoteChangeRelation) {
                        $result .= Html::a(
                            $productQuoteChangeRelation->pqcr_pq_id,
                            ['/product/product-quote-crud/view', 'id' => $productQuoteChangeRelation->pqcr_pq_id],
                            ['target' => '_blank', 'data-pjax' => 0],
                        ) . '<br />';
                    }
                    return $result;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'pqc_created_dt',
                'class' => DateTimeColumn::class
            ],
            [
                'attribute' => 'pqc_updated_dt',
                'class' => DateTimeColumn::class
            ],
            [
                'attribute' => 'pqc_decision_dt',
                'class' => DateTimeColumn::class
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
