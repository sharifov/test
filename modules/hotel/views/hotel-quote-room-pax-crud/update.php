<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteRoomPax */

$this->title = 'Update Hotel Quote Room Pax: ' . $model->hqrp_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Room Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hqrp_id, 'url' => ['view', 'id' => $model->hqrp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-quote-room-pax-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
