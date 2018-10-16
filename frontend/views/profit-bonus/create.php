<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProfitBonus */

$this->title = 'Create Profit Bonus';
$this->params['breadcrumbs'][] = ['label' => 'Profit Bonuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profit-bonus-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
