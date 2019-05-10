<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadCallExpert */

$this->title = 'Create Lead Call Expert';
$this->params['breadcrumbs'][] = ['label' => 'Lead Call Experts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-call-expert-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
