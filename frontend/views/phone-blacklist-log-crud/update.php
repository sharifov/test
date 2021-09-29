<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PhoneBlacklistLog */

$this->title = 'Update Phone Blocklist Log: ' . $model->pbll_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Blocklist Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pbll_id, 'url' => ['view', 'id' => $model->pbll_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="phone-blacklist-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
