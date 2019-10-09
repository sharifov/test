<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadQcall */

$this->title = 'Create Lead Qcall';
$this->params['breadcrumbs'][] = ['label' => 'Lead Qcalls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-qcall-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
