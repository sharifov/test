<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLine\PhoneLine */

$this->title = 'Update Phone Line: ' . $model->line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Lines', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->line_id, 'url' => ['view', 'id' => $model->line_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-line-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
