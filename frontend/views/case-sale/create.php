<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CaseSale */

$this->title = 'Create Case Sale';
$this->params['breadcrumbs'][] = ['label' => 'Case Sales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-sale-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
