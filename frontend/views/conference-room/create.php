<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceRoom */

$this->title = 'Create Conference Room';
$this->params['breadcrumbs'][] = ['label' => 'Conference Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-room-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
