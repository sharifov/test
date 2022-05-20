<?php

use yii\helpers\Html;
use common\models\ClientChatSurvey;

/* @var $this yii\web\View */
/* @var $model ClientChatSurvey */

$this->title = 'Client Chat Survey: ' . $model->ccs_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Survey', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccs_id, 'url' => ['view', 'qc_id' => $model->ccs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-survey-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
