<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceParticipant */

$this->title = 'Update Conference Participant: ' . $model->cp_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Participants', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cp_id, 'url' => ['view', 'id' => $model->cp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="conference-participant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
