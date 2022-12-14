<?php

use modules\order\src\processManager\phoneToBook\OrderProcessManager;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model OrderProcessManager */

$this->title = 'Update Order Process Manager: ' . $model->opm_id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->opm_id, 'url' => ['view', 'id' => $model->opm_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
