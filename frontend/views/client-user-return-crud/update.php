<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\clientUserReturn\entity\ClientUserReturn */

$this->title = 'Update Client User Return: ' . $model->cur_client_id;
$this->params['breadcrumbs'][] = ['label' => 'Client User Returns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cur_client_id, 'url' => ['view', 'cur_client_id' => $model->cur_client_id, 'cur_user_id' => $model->cur_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-user-return-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
