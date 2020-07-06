<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatMessage\entity\ClientChatMessage */

$this->title = 'Update Client Chat Message: ' . $model->ccm_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccm_id, 'url' => ['view', 'id' => $model->ccm_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
