<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StatusWeight */

$this->title = 'Create Status Weight';
$this->params['breadcrumbs'][] = ['label' => 'Status Weight', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="status-weight-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
