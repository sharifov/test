<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadData\entity\LeadData */

$this->title = 'Create Lead Data';
$this->params['breadcrumbs'][] = ['label' => 'Lead Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
