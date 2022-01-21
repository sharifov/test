<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadRequest\entity\LeadRequest */

$this->title = 'Update Lead Request: ' . $model->lr_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lr_id, 'url' => ['view', 'id' => $model->lr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
