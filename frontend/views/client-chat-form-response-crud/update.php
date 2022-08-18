<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatFormResponse\entity\clientChatFormResponse */

$this->title = 'Update Client Chat Form Response: ' . $model->ccfr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Form Response', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccfr_id, 'url' => ['view', 'id' => $model->ccfr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-form-response-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
