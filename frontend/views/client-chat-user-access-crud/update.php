<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserAccess\entity\ClientChatUserAccess */

$this->title = 'Update Client Chat User Access: ' . $model->ccua_cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccua_cch_id, 'url' => ['view', 'ccua_cch_id' => $model->ccua_cch_id, 'ccua_user_id' => $model->ccua_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-user-access-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
