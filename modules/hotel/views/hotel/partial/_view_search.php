<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\Hotel */

\yii\web\YiiAsset::register($this);
?>
<div class="hotel-view-search">

    <h2>Hotel Request ID: <?= Html::encode($model->ph_id) ?></h2>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ph_id], ['class' => 'btn btn-primary']) ?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->ph_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'ph_id',
            //'ph_product_id',
            'ph_check_in_date',
            'ph_check_out_date',
            'ph_destination_code',
            'ph_min_star_rate',
            'ph_max_star_rate',
            'ph_max_price_rate',
            'ph_min_price_rate',
        ],
    ]) ?>

</div>
