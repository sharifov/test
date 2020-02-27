<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteServiceLog */

$this->title = 'Update Hotel Quote Service Log: ' . $model->hqsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quote Service Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hqsl_id, 'url' => ['view', 'id' => $model->hqsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-quote-service-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
