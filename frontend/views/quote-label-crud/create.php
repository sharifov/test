<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\quoteLabel\entity\QuoteLabel */

$this->title = 'Create Quote Label';
$this->params['breadcrumbs'][] = ['label' => 'Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-label-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
