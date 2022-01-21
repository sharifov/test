<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedback */

$this->title = 'Update User Feedback: ' . $model->uf_id;
$this->params['breadcrumbs'][] = ['label' => 'User Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uf_id, 'url' => ['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
