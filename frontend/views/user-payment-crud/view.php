<?php

use sales\model\user\entity\paymentCategory\UserPaymentCategory;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\payment\UserPayment */

$this->title = $model->uptAssignedUser->username;
$this->title .= $model->uptCategory ? ' - ' . $model->uptCategory->upc_name : '';
$this->title .= ' - ' . UserPaymentCategory::getStatusName($model->upt_status_id);
$this->params['breadcrumbs'][] = ['label' => 'User Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-payment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->upt_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->upt_id], [
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
            'upt_id',
            'upt_assigned_user_id:userName',
            'upt_category_id:UserPaymentCategoryName',
            'upt_status_id:UserPaymentStatusName',
            'upt_amount',
            'upt_description:text',
            'upt_date:date',
            'upt_created_user_id:userName',
            'upt_updated_user_id:userName',
            'upt_created_dt:byUserDateTime',
            'upt_updated_dt:byUserDateTime',
            'upt_payroll_id',
        ],
    ]) ?>

</div>
