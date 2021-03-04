<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\Attraction */

$this->title = $model->atn_id;
$this->params['breadcrumbs'][] = ['label' => 'Attractions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="attraction-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->atn_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->atn_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
        <div class="col-5">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'atn_id',
                    'atn_product_id',
                    'atn_date_from:date',
                    'atn_date_to:date',
                    'atn_destination',
                    'atn_destination_code',
                    'atn_request_hash_key',
                ],
            ]) ?>
        </div>
    </div>
</div>
