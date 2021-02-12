<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruise\Cruise */

$this->title = 'Update Cruise: ' . $model->crs_id;
$this->params['breadcrumbs'][] = ['label' => 'Cruises', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crs_id, 'url' => ['view', 'id' => $model->crs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cruise-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
