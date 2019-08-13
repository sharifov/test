<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentPhoneProject */

$this->title = 'Update Department Phone Project: ' . $model->dpp_id;
$this->params['breadcrumbs'][] = ['label' => 'Department Phone Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dpp_id, 'url' => ['view', 'id' => $model->dpp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="department-phone-project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
