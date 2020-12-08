<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse */

$this->title = 'Create Client Chat Canned Response';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Canned Responses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-canned-response-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
