<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QcallConfig */

$this->title = 'Update Qcall Config: ' . $model->qc_status_id;
$this->params['breadcrumbs'][] = ['label' => 'Qcall Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qc_status_id, 'url' => ['view', 'qc_status_id' => $model->qc_status_id, 'qc_call_att' => $model->qc_call_att]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qcall-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
