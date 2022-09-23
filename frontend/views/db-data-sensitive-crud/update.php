<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\dbDataSensitive\entity\DbDataSensitive */

$this->title = 'Update DB Data Sensitive: ' . $model->dda_name;
$this->params['breadcrumbs'][] = ['label' => 'DB Data Sensitive', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dda_name, 'url' => ['view', 'id' => $model->dda_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="date-sensitive-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
