<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessing\entity\LeadPoorProcessing */

$this->title = 'Create Lead Poor Processing';
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-poor-processing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
