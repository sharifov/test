<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatFormResponse\entity\clientChatFormResponse */

$this->title = 'Create Client Chat Form Response';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Form Response', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-form-response-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
