<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin */

$this->title = $model->pqo_product_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Origins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-origin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'pqo_product_id' => $model->pqo_product_id, 'pqo_quote_id' => $model->pqo_quote_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'pqo_product_id' => $model->pqo_product_id, 'pqo_quote_id' => $model->pqo_quote_id], [
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
                'pqo_product_id',
                'pqo_quote_id',
            ],
        ]) ?>

    </div>

</div>
