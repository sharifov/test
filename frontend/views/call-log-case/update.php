<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogCase\CallLogCase */

$this->title = 'Update Call Log Case: ' . $model->clc_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->clc_cl_id, 'url' => ['view', 'clc_cl_id' => $model->clc_cl_id, 'clc_case_id' => $model->clc_case_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-log-case-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
