<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadRequest\entity\LeadRequest */

$this->title = 'Create Lead Request';
$this->params['breadcrumbs'][] = ['label' => 'Lead Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
