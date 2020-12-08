<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory */

$this->title = 'Update Client Chat Canned Response Category: ' . $model->crc_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Canned Response Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crc_id, 'url' => ['view', 'id' => $model->crc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-canned-response-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
