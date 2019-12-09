<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PhoneBlacklist */

$this->title = 'Update Phone Blacklist: ' . $model->pbl_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Blacklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pbl_id, 'url' => ['view', 'id' => $model->pbl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-blacklist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
