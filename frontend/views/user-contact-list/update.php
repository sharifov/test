<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserContactList */

$this->title = 'Update User Contact List: ' . $model->ucl_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Contact Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ucl_user_id, 'url' => ['view', 'ucl_user_id' => $model->ucl_user_id, 'ucl_client_id' => $model->ucl_client_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-contact-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
