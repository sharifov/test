<?php

use sales\model\leadProduct\entity\LeadProduct;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model LeadProduct */

$this->title = 'Update Lead Product: ' . $model->lp_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Product', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lp_lead_id, 'url' => ['view', 'lp_lead_id' => $model->lp_lead_id, 'lp_product_id' => $model->lp_product_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
