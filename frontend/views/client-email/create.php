<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ClientEmail */

$this->title = 'Create Client Email';
$this->params['breadcrumbs'][] = ['label' => 'Client Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-email-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
