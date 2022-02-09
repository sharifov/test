<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserRating\entity\LeadUserRating */

$this->title = 'Update Lead User Rating: ' . $model->lur_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead User Ratings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lur_lead_id, 'url' => ['view', 'lur_lead_id' => $model->lur_lead_id, 'lur_user_id' => $model->lur_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
    <div class="lead-user-rating-update">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>