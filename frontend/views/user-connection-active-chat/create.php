<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userConnectionActiveChat\UserConnectionActiveChat */

$this->title = 'Create User Connection Active Chat';
$this->params['breadcrumbs'][] = ['label' => 'User Connection Active Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-connection-active-chat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
