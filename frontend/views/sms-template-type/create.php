<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SmsTemplateType */

$this->title = 'Create Sms Template Type';
$this->params['breadcrumbs'][] = ['label' => 'Sms Template Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-template-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
