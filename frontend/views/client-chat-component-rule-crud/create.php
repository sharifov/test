<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\componentRule\entity\ClientChatComponentRule */

$this->title = 'Create Client Chat Component Rule';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Component Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-component-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
