<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\leadData\entity\LeadData */

$this->title = 'Update Lead Data: ' . $model->ld_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ld_id, 'url' => ['view', 'id' => $model->ld_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
