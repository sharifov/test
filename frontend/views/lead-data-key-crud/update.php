<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadDataKey\entity\LeadDataKey */

$this->title = 'Update Lead Data Key: ' . $model->ldk_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Data Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ldk_id, 'url' => ['view', 'id' => $model->ldk_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-data-key-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
