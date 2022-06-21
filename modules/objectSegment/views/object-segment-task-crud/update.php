<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\objectSegment\src\entities\ObjectSegmentTask */

$this->title = 'Update Object Segment Task: ' . $model->ostl_osl_id;
$this->params['breadcrumbs'][] = ['label' => 'Object Segment Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ostl_osl_id, 'url' => ['view', 'ostl_osl_id' => $model->ostl_osl_id, 'ostl_tl_id' => $model->ostl_tl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="object-segment-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
