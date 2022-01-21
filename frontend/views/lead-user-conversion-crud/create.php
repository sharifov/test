<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserConversion\entity\LeadUserConversion */

$this->title = 'Create Lead User Conversion';
$this->params['breadcrumbs'][] = ['label' => 'Lead User Conversions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-user-conversion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
