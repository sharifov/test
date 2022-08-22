<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTask */

$this->title = 'Update Object Task: ' . $model->ot_uuid;
$this->params['breadcrumbs'][] = ['label' => 'Object Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ot_uuid, 'url' => ['view', 'ot_uuid' => $model->ot_uuid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="object-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
