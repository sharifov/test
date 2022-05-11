<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\objectSegment\src\forms\ObjectSegmentListForm */
/* @var $osl \modules\objectSegment\src\entities\ObjectSegmentList*/

$this->title = 'Create Object Segment Rule';
$this->params['breadcrumbs'][] = ['label' => 'Object Segment List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'osl' => $osl
    ]) ?>

</div>
