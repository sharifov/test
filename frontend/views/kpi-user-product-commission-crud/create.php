<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiUserProductCommission\KpiUserProductCommission */

$this->title = 'Create Kpi User Product Commission';
$this->params['breadcrumbs'][] = ['label' => 'Kpi User Product Commissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-user-product-commission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
