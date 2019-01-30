<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserCallStatus */

$this->title = 'Update User Call Status: ' . $model->us_id;
$this->params['breadcrumbs'][] = ['label' => 'User Call Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->us_id, 'url' => ['view', 'id' => $model->us_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-call-status-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
