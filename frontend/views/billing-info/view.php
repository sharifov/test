<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BillingInfo */

$this->title = $model->bi_id;
$this->params['breadcrumbs'][] = ['label' => 'Billing Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="billing-info-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->bi_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->bi_id], [
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
            'bi_id',
            'bi_first_name',
            'bi_last_name',
            'bi_middle_name',
            'bi_company_name',
            'bi_address_line1',
            'bi_address_line2',
            'bi_city',
            'bi_state',
            'bi_country',
            'bi_zip',
            'bi_contact_phone',
            'bi_contact_email:email',
            'bi_contact_name',
            'bi_payment_method_id',
            'bi_cc_id',
            'bi_order_id',
            'bi_status_id',
            'bi_created_user_id',
            'bi_updated_user_id',
            'bi_created_dt',
            'bi_updated_dt',
        ],
    ]) ?>

</div>
