<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannel\entity\ClientChatChannel */

$this->title = 'Update Client Chat Channel: ' . $model->ccc_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccc_id, 'url' => ['view', 'id' => $model->ccc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-channel-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
