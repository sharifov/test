<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\objectSegment\src\entities\ObjectSegmentTask */

$this->title = 'Create Object Segment Task';
$this->params['breadcrumbs'][] = ['label' => 'Object Segment Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-segment-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
