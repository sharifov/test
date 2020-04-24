<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ClientProject */

$this->title = 'Update Client Project: ' . $model->cp_client_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cp_client_id, 'url' => ['view', 'cp_client_id' => $model->cp_client_id, 'cp_project_id' => $model->cp_project_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
