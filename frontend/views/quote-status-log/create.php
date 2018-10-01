<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\QuoteStatusLog */

$this->title = 'Create Quote Status Log';
$this->params['breadcrumbs'][] = ['label' => 'Quote Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
