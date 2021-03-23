<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productHolder\ProductHolder */

$this->title = 'Create Product Holder';
$this->params['breadcrumbs'][] = ['label' => 'Product Holders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-holder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
