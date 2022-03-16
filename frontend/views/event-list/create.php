<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventList */

$this->title = 'Create Event List';
$this->params['breadcrumbs'][] = ['label' => 'Event Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
