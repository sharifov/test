<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\objectSegment\src\forms\ObjectSegmentListForm */
/* @var $osl \modules\objectSegment\src\entities\ObjectSegmentList */

$this->title = 'Update Policy: "' . $model->osl_title . '" (' . $model->osl_id . ') ';
$this->params['breadcrumbs'][] = ['label' => 'Object Segment', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="abac-policy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'osl' => $osl
    ]) ?>

</div>
