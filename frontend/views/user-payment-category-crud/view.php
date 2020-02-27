<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\paymentCategory\UserPaymentCategory */

$this->title = $model->upc_name;
$this->params['breadcrumbs'][] = ['label' => 'User Payment Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-payment-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->upc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->upc_id], [
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
            'upc_id',
            'upc_name',
            'upc_description',
            'upc_enabled:booleanByLabel',
            'upc_created_user_id:userName',
            'upc_updated_user_id:userName',
            'upc_created_dt:byUserDateTime',
            'upc_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
