<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelRoom */

$this->title = 'Update Hotel Room: ' . $model->hr_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hr_id, 'url' => ['view', 'id' => $model->hr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-room-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
