<?php

use sales\model\leadProduct\entity\LeadProduct;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model LeadProduct */

$this->title = 'Create Lead Product';
$this->params['breadcrumbs'][] = ['label' => 'Lead Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
