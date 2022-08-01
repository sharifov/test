<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\smartLeadDistribution\src\entities\LeadRatingParameter */

$this->title = 'Create Lead Rating Parameter';
$this->params['breadcrumbs'][] = ['label' => 'Lead Rating Parameters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-rating-parameter-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
