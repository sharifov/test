<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserCallStatus */

$this->title = 'Create User Call Status';
$this->params['breadcrumbs'][] = ['label' => 'User Call Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-call-status-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
