<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productQuoteOption\ProductQuoteOption */

$this->title = $model->pqo_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-option-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pqo_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pqo_id], [
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
            'pqo_id',
            'pqoProductQuote:productQuote',
            'pqo_product_option_id',
            'pqo_name',
            'pqo_description:ntext',
            'pqo_status_id:productQuoteOptionStatus',
            'pqo_price',
            'pqo_client_price',
            'pqo_extra_markup',
            'pqo_created_user_id:userName',
            'pqo_updated_user_id:userName',
            'pqo_created_dt:byUserDateTime',
            'pqo_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
