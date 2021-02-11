<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCar\RentCar */

$this->title = 'Create Rent Car';
$this->params['breadcrumbs'][] = ['label' => 'Rent Cars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rent-car-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
