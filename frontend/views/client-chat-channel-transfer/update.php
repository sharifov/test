<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer */

$this->title = 'Update Client Chat Channel Transfer: ' . $model->cctr_from_ccc_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channel Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cctr_from_ccc_id, 'url' => ['view', 'cctr_from_ccc_id' => $model->cctr_from_ccc_id, 'cctr_to_ccc_id' => $model->cctr_to_ccc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-channel-transfer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
