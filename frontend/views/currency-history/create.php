<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyHistory */

$this->title = 'Create Currency History';
$this->params['breadcrumbs'][] = ['label' => 'Currency Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
