<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProfitBonus */

$this->title = 'Update Profit Bonus: ' . $model->pb_id;
$this->params['breadcrumbs'][] = ['label' => 'Profit Bonuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pb_id, 'url' => ['view', 'id' => $model->pb_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="profit-bonus-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
