<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteRoom */

$this->title = 'Update Hotel Quote Room: ' . $model->hqr_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hqr_id, 'url' => ['view', 'id' => $model->hqr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-quote-room-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
