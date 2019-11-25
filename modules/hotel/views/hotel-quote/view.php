<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuote */

$this->title = $model->hq_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hotel-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->hq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->hq_id], [
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
            'hq_id',
            'hq_hotel_id',
            'hq_hash_key',
            'hq_product_quote_id',
            'hq_json_response',
            'hq_destination_name',
            'hq_hotel_name',
            'hq_hotel_list_id',
        ],
    ]) ?>

</div>
