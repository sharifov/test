<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DateSensitive */

$this->title = 'Create Date Sensitive';
$this->params['breadcrumbs'][] = ['label' => 'Date Sensitive', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="date-sensitive-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
