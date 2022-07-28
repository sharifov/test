<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\smartLeadDistribution\src\entities\LeadRatingParameter */

$this->title = 'Update Lead Rating Parameter: ' . $model->lrp_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Rating Parameters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lrp_id, 'url' => ['view', 'lrp_id' => $model->lrp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-rating-parameter-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
