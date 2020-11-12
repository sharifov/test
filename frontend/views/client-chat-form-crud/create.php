<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatForm\entity\ClientChatForm */

$this->title = 'Create Client Chat Form';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_builder', [
        'model' => $model,
    ]) ?>

</div>
