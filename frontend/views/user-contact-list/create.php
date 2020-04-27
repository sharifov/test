<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserContactList */

$this->title = 'Create User Contact List';
$this->params['breadcrumbs'][] = ['label' => 'User Contact Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contact-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
