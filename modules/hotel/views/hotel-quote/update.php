<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuote */

$this->title = 'Update Hotel Quote: ' . $model->hq_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hq_id, 'url' => ['view', 'id' => $model->hq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
