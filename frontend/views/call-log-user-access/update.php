<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess */

$this->title = 'Update Call Log User Access: ' . $model->clua_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->clua_id, 'url' => ['view', 'id' => $model->clua_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-log-user-access-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
