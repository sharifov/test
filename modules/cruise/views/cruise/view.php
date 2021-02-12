<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruise\Cruise */

$this->title = $model->crs_id;
$this->params['breadcrumbs'][] = ['label' => 'Cruises', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cruise-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->crs_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->crs_id], [
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
                'crs_id',
                'crs_product_id',
                'crs_departure_date_from',
                'crs_arrival_date_to',
                'crs_destination_code',
                'crs_destination_label',
            ],
        ]) ?>

    </div>

</div>
