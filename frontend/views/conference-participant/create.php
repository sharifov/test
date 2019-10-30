<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceParticipant */

$this->title = 'Create Conference Participant';
$this->params['breadcrumbs'][] = ['label' => 'Conference Participants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-participant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
