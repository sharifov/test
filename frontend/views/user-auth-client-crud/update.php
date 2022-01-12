<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\userAuthClient\entity\UserAuthClient */

$this->title = 'Update Auth Client: ' . $model->uac_id;
$this->params['breadcrumbs'][] = ['label' => 'Auth Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uac_id, 'url' => ['view', 'uac_id' => $model->uac_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
