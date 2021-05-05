<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\caseOrder\entity\CaseOrder */

$this->title = 'Create Case Order';
$this->params['breadcrumbs'][] = ['label' => 'Case Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
