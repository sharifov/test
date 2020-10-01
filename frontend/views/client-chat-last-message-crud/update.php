<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatLastMessage\entity\ClientChatLastMessage */

$this->title = 'Update Client Chat Last Message: ' . $model->cclm_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Last Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cclm_id, 'url' => ['view', 'id' => $model->cclm_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-last-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
