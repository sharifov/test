<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLog\CallLog */

$this->title = 'Update Call Log: ' . $model->cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cl_id, 'url' => ['view', 'id' => $model->cl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
