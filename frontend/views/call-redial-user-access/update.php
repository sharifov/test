<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadRedial\entity\CallRedialUserAccess */

$this->title = 'Update Call Redial User Access: ' . $model->crua_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Redial User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crua_lead_id . ', ' . $model->crua_user_id, 'url' => ['view', 'crua_lead_id' => $model->crua_lead_id, 'crua_user_id' => $model->crua_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-redial-user-access-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
