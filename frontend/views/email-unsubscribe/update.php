<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EmailUnsubscribe */

$this->title = 'Update Email Unsubscribe: ' . $model->eu_email;
$this->params['breadcrumbs'][] = ['label' => 'Email Unsubscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->eu_email, 'url' => ['view', 'eu_email' => $model->eu_email, 'eu_project_id' => $model->eu_project_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-unsubscribe-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('partial/_form', [
        'model' => $model,
    ]) ?>

</div>
