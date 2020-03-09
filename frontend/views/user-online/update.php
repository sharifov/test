<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserOnline */

$this->title = 'Update User Online: ' . $model->uo_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Onlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uo_user_id, 'url' => ['view', 'id' => $model->uo_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-online-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
