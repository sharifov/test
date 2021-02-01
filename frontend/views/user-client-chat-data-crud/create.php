<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\userClientChatData\entity\UserClientChatData */

$this->title = 'Create User Client Chat Data';
$this->params['breadcrumbs'][] = ['label' => 'User Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-client-chat-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
