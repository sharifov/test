<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\objectSegment\src\forms\ObjectSegmentRuleForm */
/* @var $osr \modules\objectSegment\src\entities\ObjectSegmentRule */

$this->title = 'Update Policy: "' . $model->osr_title . '" (' . $model->osr_id . ') ';
$this->params['breadcrumbs'][] = ['label' => 'Object Segment Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $osr->osrObjectSegmentList->osl_title, 'url' => ['view', 'id' => $osr->osrObjectSegmentList->osl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="abac-policy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'osr' => $osr
    ]) ?>

</div>
