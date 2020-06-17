<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserChannel\entity\ClientChatUserChannel */

$this->title = 'Update Client Chat User Channel: ' . $model->ccuc_user_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat User Channels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccuc_user_id, 'url' => ['view', 'ccuc_user_id' => $model->ccuc_user_id, 'ccuc_channel_id' => $model->ccuc_channel_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-user-channel-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
