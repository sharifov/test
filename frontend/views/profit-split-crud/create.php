<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProfitSplit */

$this->title = 'Create Profit Split';
$this->params['breadcrumbs'][] = ['label' => 'Profit Split', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profit-split-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
