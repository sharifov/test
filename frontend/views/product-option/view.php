<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProductOption */

$this->title = $model->po_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-option-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->po_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->po_id], [
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
            'po_id',
            'po_key',
            'po_product_type_id',
            'po_name',
            'po_description:ntext',
            'po_price_type_id',
            'po_max_price',
            'po_min_price',
            'po_price',
            'po_enabled',
            'po_created_user_id',
            'po_updated_user_id',
            [
                'attribute' => 'po_created_dt',
                'value' => static function(\common\models\ProductOption $model) {
                    return $model->po_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->po_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'po_updated_dt',
                'value' => static function(\common\models\ProductOption $model) {
                    return $model->po_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->po_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
