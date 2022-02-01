<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingData\entity\LeadPoorProcessingData */

$this->title = 'Update Lead Poor Processing Data: ' . $model->lppd_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lppd_id, 'url' => ['view', 'lppd_id' => $model->lppd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-poor-processing-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
