<?php

use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponProduct\CouponProduct */

$this->title = $model->cup_coupon_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="coupon-product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'cup_coupon_id' => $model->cup_coupon_id, 'cup_product_type_id' => $model->cup_product_type_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cup_coupon_id' => $model->cup_coupon_id, 'cup_product_type_id' => $model->cup_product_type_id], [
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
                'cup_coupon_id',
                'cup_product_type_id',
            ],
        ]) ?>

    </div>

     <div class="col-md-6">
        <strong><?php echo $model->getAttributeLabel('cup_data_json') ?></strong><br />
        <pre><small><?php VarDumper::dump($model->cup_data_json, 20, true); ?></small></pre><br />
    </div>

</div>
