<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DateSensitive */

$this->title = 'Update Date Sensitive: ' . $model->da_name;
$this->params['breadcrumbs'][] = ['label' => 'Date Sensitive', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->da_name, 'url' => ['view', 'id' => $model->da_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="date-sensitive-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
