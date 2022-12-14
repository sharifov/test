<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\entities\cases\CaseStatusLog */

$this->title = 'Create Case Status Log';
$this->params['breadcrumbs'][] = ['label' => 'Cases Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
