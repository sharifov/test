<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\sms\entity\smsDistributionList\SmsDistributionList */

$this->title = 'Update Sms Distribution List: ' . $model->sdl_id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Distribution Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sdl_id, 'url' => ['view', 'id' => $model->sdl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sms-distribution-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
