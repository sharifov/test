<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserParams */

$this->title = 'Update User Params: ' . $model->up_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->up_user_id, 'url' => ['view', 'id' => $model->up_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-params-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
