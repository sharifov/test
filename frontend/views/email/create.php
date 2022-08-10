<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $emailForm src\entities\email\form\EmailCreateForm */

$this->title = 'Create Email';
$this->params['breadcrumbs'][] = ['label' => 'Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_email_form', [
        'emailForm' => $emailForm,
    ]) ?>

</div>
