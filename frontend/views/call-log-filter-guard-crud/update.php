<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLogFilterGuard\entity\CallLogFilterGuard */

$this->title = 'Update Call Log Filter Guard: ' . $model->clfg_call_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Filter Guards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->clfg_call_id, 'url' => ['view', 'id' => $model->clfg_call_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-log-filter-guard-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
