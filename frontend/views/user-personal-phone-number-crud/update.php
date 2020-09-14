<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\userPersonalPhoneNumber\entity\UserPersonalPhoneNumber */

$this->title = 'Update User Personal Phone Number: ' . $model->upn_id;
$this->params['breadcrumbs'][] = ['label' => 'User Personal Phone Numbers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->upn_id, 'url' => ['view', 'id' => $model->upn_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-personal-phone-number-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
