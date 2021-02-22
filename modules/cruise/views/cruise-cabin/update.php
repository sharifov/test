<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabin\CruiseCabin */

$this->title = 'Update Cruise Cabin: ' . $model->crc_id;
$this->params['breadcrumbs'][] = ['label' => 'Cruise Cabins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crc_id, 'url' => ['view', 'id' => $model->crc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cruise-cabin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
