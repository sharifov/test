<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceParticipantStats\ConferenceParticipantStats */

$this->title = 'Create Conference Participant Stats';
$this->params['breadcrumbs'][] = ['label' => 'Conference Participant Stats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-participant-stats-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
