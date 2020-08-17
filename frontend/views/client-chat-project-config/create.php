<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig */

$this->title = 'Create Client Chat Project Config';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Project Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-project-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
