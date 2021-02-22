<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax */

$this->title = 'Update Cruise Cabin Pax: ' . $model->crp_id;
$this->params['breadcrumbs'][] = ['label' => 'Cruise Cabin Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crp_id, 'url' => ['view', 'id' => $model->crp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cruise-cabin-pax-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
