<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientDataKey\entity\ClientDataKey */

$this->title = 'Update Client Data Key: ' . $model->cdk_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Data Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cdk_id, 'url' => ['view', 'id' => $model->cdk_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-data-key-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
