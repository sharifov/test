<?php

use common\models\Payment;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Payment */

$this->title = $model->pay_id;
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pay_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pay_id], [
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
            'pay_id',
            'pay_type_id',
            'pay_method_id',
            [
                'attribute' => 'pay_status_id',
                'value' => static function (Payment $model) {
                    return Payment::getStatusName($model->pay_status_id);
                },
            ],
            'pay_code',
            'pay_date',
            'pay_amount',
            'pay_currency',
            'pay_invoice_id',
            'pay_order_id',
            'pay_billing_id',
            'pay_description',
            'pay_created_user_id',
            'pay_updated_user_id',
            'pay_created_dt',
            'pay_updated_dt',
        ],
    ]) ?>

</div>
