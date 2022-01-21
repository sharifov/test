<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadDataKey\entity\LeadDataKey */

$this->title = 'Create Lead Data Key';
$this->params['breadcrumbs'][] = ['label' => 'Lead Data Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-data-key-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
