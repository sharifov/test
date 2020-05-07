<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\coupon\Coupon */

$this->title = $model->c_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="coupon-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->c_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->c_id], [
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
                'c_id',
                'c_code',
                'c_amount',
                'c_currency_code',
                'c_percent',
                'c_exp_date',
                'c_start_date',
                'c_reusable',
                'c_reusable_count',
                'c_public',
                'c_status_id',
                'c_used_dt',
                'c_disabled',
                'c_type_id',
                'c_created_dt',
                'c_updated_dt',
                'c_created_user_id',
                'c_updated_user_id',
            ],
        ]) ?>

    </div>

</div>
