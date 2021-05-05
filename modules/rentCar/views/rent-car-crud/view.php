<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCar\RentCar */

$this->title = $model->prc_id;
$this->params['breadcrumbs'][] = ['label' => 'Rent Cars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="rent-car-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->prc_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->prc_id], [
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
                'prc_id',
                'prc_request_hash_key',
                'prc_product_id',
                'prc_pick_up_code',
                'prc_drop_off_code',
                'prc_pick_up_date',
                'prc_drop_off_date',
                'prc_pick_up_time',
                'prc_drop_off_time',
                'prc_created_dt:byUserDateTime',
                'prc_updated_dt:byUserDateTime',
                'prc_created_user_id:userName',
                'prc_updated_user_id:userName',
            ],
        ]) ?>

    </div>

</div>
