<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CaseSale */

$this->title = 'Update Case Sale: ' . $model->css_cs_id;
$this->params['breadcrumbs'][] = ['label' => 'Case Sales', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->css_cs_id, 'url' => ['view', 'css_cs_id' => $model->css_cs_id, 'css_sale_id' => $model->css_sale_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="case-sale-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
