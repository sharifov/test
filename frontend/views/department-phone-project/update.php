<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentPhoneProject */

$this->title = 'Update Department Phone Project: ' . $model->dpp_dep_id;
$this->params['breadcrumbs'][] = ['label' => 'Department Phone Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dpp_dep_id, 'url' => ['view', 'dpp_dep_id' => $model->dpp_dep_id, 'dpp_project_id' => $model->dpp_project_id, 'dpp_phone_number' => $model->dpp_phone_number]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="department-phone-project-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
