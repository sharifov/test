<?php

use common\components\grid\DateTimeColumn;
use modules\product\src\entities\productQuoteData\ProductQuoteData;
use modules\product\src\entities\productQuoteData\ProductQuoteDataKey;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteData\search\ProductQuoteDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pqd_id',
            [
                'class' => \modules\product\src\grid\columns\ProductQuoteColumn::class,
                'attribute' => 'pqd_product_quote_id',
                'relation' => 'pqdProductQuote',
            ],
            [
                'attribute' => 'pqd_key',
                'value' => static function (ProductQuoteData $model) {
                    return ProductQuoteDataKey::asFormat($model->pqd_key);
                },
                'format' => 'raw'
            ],
            'pqd_value',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqd_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqd_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
