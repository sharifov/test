<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $id int */
/* @var $emailForm src\entities\email\form\EmailForm */

$this->title = 'Update Email: ' . $id;
$this->params['breadcrumbs'][] = ['label' => 'Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $id, 'url' => ['view', 'id' => $id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('/email/_email_form', [
        'emailForm' => $emailForm,
    ]) ?>

</div>
