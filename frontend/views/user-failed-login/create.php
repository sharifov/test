<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserFailedLogin */

$this->title = 'Create User Failed Login';
$this->params['breadcrumbs'][] = ['label' => 'User Failed Logins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-failed-login-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
