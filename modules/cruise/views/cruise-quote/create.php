<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseQuote\CruiseQuote */

$this->title = 'Create Cruise Quote';
$this->params['breadcrumbs'][] = ['label' => 'Cruise Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cruise-quote-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
