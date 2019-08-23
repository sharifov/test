<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CallUserAccess */

$this->title = 'Update Call User Access: ' . $model->cua_call_id;
$this->params['breadcrumbs'][] = ['label' => 'Call User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cua_call_id, 'url' => ['view', 'cua_call_id' => $model->cua_call_id, 'cua_user_id' => $model->cua_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-user-access-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
