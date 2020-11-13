<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceEventLog\ConferenceEventLog */

$this->title = 'Create Conference Event Log';
$this->params['breadcrumbs'][] = ['label' => 'Conference Event Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-event-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
