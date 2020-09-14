<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats */

$this->title = 'Update Conference Participant Stats: ' . $model->cps_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Participant Stats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cps_id, 'url' => ['view', 'id' => $model->cps_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="conference-participant-stats-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
