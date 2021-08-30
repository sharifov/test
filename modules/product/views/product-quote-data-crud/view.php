<?php

use common\components\grid\DateTimeColumn;
use modules\product\src\entities\productQuoteData\ProductQuoteData;
use modules\product\src\entities\productQuoteData\ProductQuoteDataKey;
use modules\product\src\grid\columns\ProductQuoteColumn;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteData\ProductQuoteData */

$this->title = $model->pqd_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pqd_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pqd_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pqd_id',
            [
                'class' => ProductQuoteColumn::class,
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
            'pqd_created_dt:byUserDateTime',
            'pqd_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
