<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userStatus\UserStatus */

$this->title = 'Update User Status: ' . $model->us_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->us_user_id, 'url' => ['view', 'id' => $model->us_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-status-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
