<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileProductQuote\FileProductQuote */

$this->title = 'Create File Product Quote';
$this->params['breadcrumbs'][] = ['label' => 'File Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-product-quote-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
