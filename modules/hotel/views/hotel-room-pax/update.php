<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelRoomPax */

$this->title = 'Update Hotel Room Pax: ' . $model->hrp_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Room Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hrp_id, 'url' => ['view', 'id' => $model->hrp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-room-pax-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
