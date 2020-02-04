<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\ProductTypePaymentMethod\ProductTypePaymentMethod */

$this->title = $model->ptpm_produt_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Type Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-type-payment-method-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ptpm_produt_type_id' => $model->ptpm_produt_type_id, 'ptpm_payment_method_id' => $model->ptpm_payment_method_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ptpm_produt_type_id' => $model->ptpm_produt_type_id, 'ptpm_payment_method_id' => $model->ptpm_payment_method_id], [
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
            'ptpm_produt_type_id',
            'ptpm_payment_method_id',
            'ptpm_payment_fee_percent',
            'ptpm_payment_fee_amount',
            'ptpm_enabled',
            'ptpm_default',
            'ptpm_created_user_id',
            'ptpm_updated_user_id',
            'ptpm_created_dt',
            'ptpm_updated_dt',
        ],
    ]) ?>

</div>
