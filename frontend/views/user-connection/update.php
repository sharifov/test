<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserConnection */

$this->title = 'Update User Connection: ' . $model->uc_id;
$this->params['breadcrumbs'][] = ['label' => 'User Connections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uc_id, 'url' => ['view', 'id' => $model->uc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-connection-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
