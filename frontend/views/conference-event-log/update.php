<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceEventLog\ConferenceEventLog */

$this->title = 'Update Conference Event Log: ' . $model->cel_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Event Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cel_id, 'url' => ['view', 'id' => $model->cel_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="conference-event-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
