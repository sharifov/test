<?php

use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderUserProfit\OrderUserProfit */

$this->title = $model->oup_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-user-profit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'oup_order_id' => $model->oup_order_id, 'oup_user_id' => $model->oup_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'oup_order_id' => $model->oup_order_id, 'oup_user_id' => $model->oup_user_id], [
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
            [
                'attribute' => 'oup_order_id',
                'value' => static function (OrderUserProfit $orderUserProfit) {
					return Html::a($orderUserProfit->oup_order_id, Url::toRoute(['/order/order-crud/view', 'id' => $orderUserProfit->oup_order_id]), [
						'target' => '_blank',
						'data-pjax' => 0
					]);
                },
                'format' => 'raw'
            ],
            'oup_user_id:userName',
            'oup_percent',
            'oup_amount',
            'oup_created_dt:byUserDateTime',
            'oup_updated_dt:byUserDateTime',
            'oup_created_user_id:userName',
            'oup_updated_user_id:userName',
        ],
    ]) ?>

</div>
