<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\userModelSetting\entity\UserModelSetting */

$this->title = 'Update User Model Setting: ' . $model->ums_id;
$this->params['breadcrumbs'][] = ['label' => 'User Model Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ums_id, 'url' => ['view', 'id' => $model->ums_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-model-setting-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
