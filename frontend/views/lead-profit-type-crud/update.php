<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadProfitType */

$this->title = 'Update Lead Profit Type: ' . $model->lpt_profit_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Profit Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lpt_profit_type_id, 'url' => ['view', 'id' => $model->lpt_profit_type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-profit-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
