<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\ClientChatSurvey */

$this->title = 'Create Client Chat Survey';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Survey', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-survey-create">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
