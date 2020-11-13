<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat */

$this->title = 'Update User Connection Active Chat: ' . $model->ucac_conn_id;
$this->params['breadcrumbs'][] = ['label' => 'User Connection Active Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ucac_conn_id, 'url' => ['view', 'ucac_conn_id' => $model->ucac_conn_id, 'ucac_chat_id' => $model->ucac_chat_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-connection-active-chat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
