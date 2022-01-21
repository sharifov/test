<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\userStatDay\entity\UserStatDay */

$this->title = 'Update User Stat Day: ' . $model->usd_id;
$this->params['breadcrumbs'][] = ['label' => 'User Stat Days', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usd_id, 'url' => ['view', 'usd_id' => $model->usd_id, 'usd_month' => $model->usd_month, 'usd_year' => $model->usd_year]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-stat-day-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
