<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserProductType */

$this->title = $model->upt_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Product Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-product-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'upt_user_id' => $model->upt_user_id, 'upt_product_type_id' => $model->upt_product_type_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'upt_user_id' => $model->upt_user_id, 'upt_product_type_id' => $model->upt_product_type_id], [
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
            'upt_user_id',
            'upt_product_type_id',
            'upt_commission_percent',
            'upt_product_enabled',
            'upt_created_user_id',
            'upt_updated_user_id',
            'upt_created_dt',
            'upt_updated_dt',
        ],
    ]) ?>

</div>
