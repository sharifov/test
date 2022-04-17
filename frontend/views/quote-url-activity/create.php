<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\QuoteUrlActivity */

$this->title = 'Create Quote Url Activity';
$this->params['breadcrumbs'][] = ['label' => 'Quote Url Activity', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-url-activity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
