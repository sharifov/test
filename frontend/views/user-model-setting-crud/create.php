<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\userModelSetting\entity\UserModelSetting */

$this->title = 'Create User Model Setting';
$this->params['breadcrumbs'][] = ['label' => 'User Model Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-model-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
