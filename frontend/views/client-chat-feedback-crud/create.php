<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatFeedback\entity\ClientChatFeedback */

$this->title = 'Create Client Chat Feedback';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Feedback', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-feedback-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
