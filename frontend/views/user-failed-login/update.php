<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserFailedLogin */

$this->title = 'Update User Failed Login: ' . $model->ufl_id;
$this->params['breadcrumbs'][] = ['label' => 'User Failed Logins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ufl_id, 'url' => ['view', 'id' => $model->ufl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-failed-login-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
