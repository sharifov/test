<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroupAssign */

$this->title = 'Update User Group Assign: ' . $model->ugs_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Group Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ugs_user_id, 'url' => ['view', 'ugs_user_id' => $model->ugs_user_id, 'ugs_group_id' => $model->ugs_group_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-group-assign-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
