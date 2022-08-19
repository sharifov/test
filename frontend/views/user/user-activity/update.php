<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\user\userActivity\entity\UserActivity */

$this->title = 'Update User Activity: ' . $model->ua_start_dt;
$this->params['breadcrumbs'][] = ['label' => 'User Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ua_start_dt, 'url' => ['view', 'ua_start_dt' => $model->ua_start_dt, 'ua_user_id' => $model->ua_user_id, 'ua_object_event' => $model->ua_object_event, 'ua_object_id' => $model->ua_object_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-activity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
