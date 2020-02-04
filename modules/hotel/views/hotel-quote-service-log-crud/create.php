<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteServiceLog */

$this->title = 'Create Hotel Quote Service Log';
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Service Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-quote-service-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
