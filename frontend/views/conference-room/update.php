<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceRoom */

$this->title = 'Update Conference Room: ' . $model->cr_id;
$this->params['breadcrumbs'][] = ['label' => 'Conference Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cr_id, 'url' => ['view', 'id' => $model->cr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="conference-room-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
