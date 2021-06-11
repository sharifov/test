<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\componentRule\entity\ClientChatComponentRule */

$this->title = 'Update Client Chat Component Rule: ' . $model->cccr_component_event_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Component Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cccr_component_event_id, 'url' => ['view', 'cccr_component_event_id' => $model->cccr_component_event_id, 'cccr_value' => $model->cccr_value, 'cccr_runnable_component' => $model->cccr_runnable_component]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-component-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
