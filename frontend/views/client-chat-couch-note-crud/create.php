<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\ClientChatCouchNote\entity\ClientChatCouchNote */

$this->title = 'Create Client Chat Couch Note';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Couch Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-couch-note-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
