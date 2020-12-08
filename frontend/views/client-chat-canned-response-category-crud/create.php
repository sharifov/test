<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory */

$this->title = 'Create Client Chat Canned Response Category';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Canned Response Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-canned-response-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
