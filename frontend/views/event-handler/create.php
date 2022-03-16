<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventHandler */

$this->title = 'Create Event Handler';
$this->params['breadcrumbs'][] = ['label' => 'Event Handlers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-handler-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
