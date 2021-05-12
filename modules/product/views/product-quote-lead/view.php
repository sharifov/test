<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteLead\ProductQuoteLead */

$this->title = $model->pql_product_quote_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-lead-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'pql_product_quote_id' => $model->pql_product_quote_id, 'pql_lead_id' => $model->pql_lead_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'pql_product_quote_id' => $model->pql_product_quote_id, 'pql_lead_id' => $model->pql_lead_id], [
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
                'pql_product_quote_id',
                'pql_lead_id',
            ],
        ]) ?>

    </div>

</div>
