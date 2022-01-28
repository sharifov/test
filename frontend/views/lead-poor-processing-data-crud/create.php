<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingData\entity\LeadPoorProcessingData */

$this->title = 'Create Lead Poor Processing Data';
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-poor-processing-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
