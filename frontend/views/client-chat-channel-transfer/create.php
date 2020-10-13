<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer */

$this->title = 'Create Client Chat Channel Transfer';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channel Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-channel-transfer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
