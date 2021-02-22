<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCar\RentCar */

$this->title = 'Update Rent Car: ' . $model->prc_id;
$this->params['breadcrumbs'][] = ['label' => 'Rent Cars', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->prc_id, 'url' => ['view', 'id' => $model->prc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="rent-car-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
