<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BillingInfo */

$this->title = 'Create Billing Info';
$this->params['breadcrumbs'][] = ['label' => 'Billing Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="billing-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
