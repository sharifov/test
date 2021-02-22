<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCarQuote\RentCarQuote */

$this->title = 'Update Rent Car Quote: ' . $model->rcq_id;
$this->params['breadcrumbs'][] = ['label' => 'Rent Car Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->rcq_id, 'url' => ['view', 'id' => $model->rcq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="rent-car-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
