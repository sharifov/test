<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\authClient\entity\AuthClient */

$this->title = 'Update Auth Client: ' . $model->ac_id;
$this->params['breadcrumbs'][] = ['label' => 'Auth Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ac_id, 'url' => ['view', 'ac_id' => $model->ac_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
