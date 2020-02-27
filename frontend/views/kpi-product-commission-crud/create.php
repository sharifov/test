<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiProductCommission\KpiProductCommission */

$this->title = 'Create Kpi Product Commission';
$this->params['breadcrumbs'][] = ['label' => 'Kpi Product Commissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-product-commission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
