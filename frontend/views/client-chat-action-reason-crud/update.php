<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\actionReason\ClientChatActionReason */

$this->title = 'Update Client Chat Action Reason: ' . $model->ccar_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Action Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccar_id, 'url' => ['view', 'id' => $model->ccar_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-action-reason-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
