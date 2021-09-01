<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatDataRequest\entity\ClientChatDataRequest */

$this->title = 'Update Client Chat Data Request: ' . $model->ccdr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Data Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccdr_id, 'url' => ['view', 'id' => $model->ccdr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-data-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
