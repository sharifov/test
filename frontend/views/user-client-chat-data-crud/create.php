<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\userClientChatData\entity\UserClientChatData */

$this->title = 'Create User Client Chat Data CRUD';
$this->params['breadcrumbs'][] = ['label' => 'User Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-client-chat-data-create">
    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="alert alert-warning" role="alert">
                  <i class="fa fa-exclamation-triangle"></i> Creation CRUD - no synchronization with RC server
            </div>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
