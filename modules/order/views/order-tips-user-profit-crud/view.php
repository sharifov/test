<?php

use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit */

$this->title = $model->otup_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Tips User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-tips-user-profit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'otup_order_id' => $model->otup_order_id, 'otup_user_id' => $model->otup_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'otup_order_id' => $model->otup_order_id, 'otup_user_id' => $model->otup_user_id], [
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
				'attribute' => 'otup_order_id',
				'value' => static function (OrderTipsUserProfit $orderUserProfit) {
					return Html::a($orderUserProfit->otup_order_id, Url::toRoute(['/order/order-crud/view', 'id' => $orderUserProfit->otup_order_id]), [
						'target' => '_blank',
						'data-pjax' => 0
					]);
				},
				'format' => 'raw'
			],
            'otup_user_id:userName',
            'otup_percent',
            'otup_amount',
            'otup_created_dt:byUserDateTime',
            'otup_updated_dt:byUserDateTime',
            'otup_created_user_id:userName',
            'otup_updated_user_id:userName',
        ],
    ]) ?>

</div>
