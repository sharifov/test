<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \sales\model\clientChat\componentEvent\form\ComponentEventCreateForm */

$this->title = 'Create Client Chat Component Event';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Component Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-component-event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
