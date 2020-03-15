<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\sms\entity\smsDistributionList\SmsDistributionList */

$this->title = 'Create Sms Distribution List';
$this->params['breadcrumbs'][] = ['label' => 'Sms Distribution Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-distribution-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
