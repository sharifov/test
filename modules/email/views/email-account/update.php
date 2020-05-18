<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\email\src\entity\emailAccount\EmailAccount */

$this->title = 'Update Email Account: ' . $model->ea_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ea_id, 'url' => ['view', 'id' => $model->ea_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
