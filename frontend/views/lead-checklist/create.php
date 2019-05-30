<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklist */

$this->title = 'Create Lead Checklist';
$this->params['breadcrumbs'][] = ['label' => 'Lead Checklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-checklist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
