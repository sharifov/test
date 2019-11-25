<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteRoom */

$this->title = 'Create Hotel Quote Room';
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-quote-room-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
