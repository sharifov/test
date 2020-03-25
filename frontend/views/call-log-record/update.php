<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogRecord\CallLogRecord */

$this->title = 'Update Call Log Record: ' . $model->clr_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->clr_cl_id, 'url' => ['view', 'id' => $model->clr_cl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-log-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
