<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\QuoteCommunicationOpenLog */

$this->title = 'Create Quote Communication Open Log';
$this->params['breadcrumbs'][] = ['label' => 'Quote Communication Open Log', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-communication-open-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
