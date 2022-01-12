<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedbackFile */

$this->title = 'Create User Feedback File';
$this->params['breadcrumbs'][] = ['label' => 'User Feedback Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-feedback-file-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
