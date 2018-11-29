<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplateType */

$this->title = 'Update Email Template Type: ' . $model->etp_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Template Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->etp_id, 'url' => ['view', 'id' => $model->etp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-template-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
