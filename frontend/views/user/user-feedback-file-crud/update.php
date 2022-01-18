<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedbackFile */

$this->title = 'Update User Feedback File: ' . $model->uff_id;
$this->params['breadcrumbs'][] = ['label' => 'User Feedback Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uff_id, 'url' => ['view', 'uff_id' => $model->uff_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-feedback-file-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
