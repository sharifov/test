<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\email\src\entity\emailAccount\EmailAccount */

$this->title = 'Create Email Account';
$this->params['breadcrumbs'][] = ['label' => 'Email Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-account-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
