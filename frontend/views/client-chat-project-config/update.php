<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig */

$this->title = 'Update Client Chat Project Config: ' . $model->ccpc_project_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Project Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccpc_project_id, 'url' => ['view', 'id' => $model->ccpc_project_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-project-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
