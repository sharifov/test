<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog */

$this->title = 'Create Lead Poor Processing Log';
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-poor-processing-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
