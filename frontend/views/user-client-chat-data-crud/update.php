<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\userClientChatData\entity\UserClientChatData */

$this->title = 'Update User Client Chat Data: ' . $model->uccd_id;
$this->params['breadcrumbs'][] = ['label' => 'User Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uccd_id, 'url' => ['view', 'id' => $model->uccd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-client-chat-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
