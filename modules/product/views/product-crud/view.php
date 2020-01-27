<?php

use modules\product\src\entities\product\Product;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\product\Product */

$this->title = $model->pr_id;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pr_id], [
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
            'pr_id',
            'pr_type_id:productType',
            'pr_name',
            [
                'attribute' => 'pr_lead_id',
                'value' => static function (Product $product) {
                    return $product->pr_lead_id ? $product->prLead : null;
                },
                'format' => 'lead',
            ],
            'pr_description:ntext',
            'pr_status_id',
            'pr_service_fee_percent',
            'pr_created_user_id:userName',
            'pr_updated_user_id:userName',
            'pr_created_dt:byUserDateTime',
            'pr_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
