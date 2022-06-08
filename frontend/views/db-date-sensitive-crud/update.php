<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DbDateSensitive */

$this->title = 'Update DB Date Sensitive: ' . $model->dda_name;
$this->params['breadcrumbs'][] = ['label' => 'DB Date Sensitive', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dda_name, 'url' => ['view', 'id' => $model->dda_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="date-sensitive-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
