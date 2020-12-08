<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse */

$this->title = 'Update Client Chat Canned Response: ' . $model->cr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Canned Responses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cr_id, 'url' => ['view', 'id' => $model->cr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-canned-response-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
