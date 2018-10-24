<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */

$this->title = 'Update User Project Params: ' . $model->upp_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Project Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->upp_user_id, 'url' => ['view', 'upp_user_id' => $model->upp_user_id, 'upp_project_id' => $model->upp_project_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-project-params-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
