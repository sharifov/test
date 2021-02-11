<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCarQuote\RentCarQuote */

$this->title = 'Create Rent Car Quote';
$this->params['breadcrumbs'][] = ['label' => 'Rent Car Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rent-car-quote-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
