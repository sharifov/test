<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\clientVisitor\entity\ClientVisitor */

$this->title = 'Update Client Visitor: ' . $model->cv_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cv_id, 'url' => ['view', 'id' => $model->cv_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-visitor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
