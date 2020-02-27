<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserProductType */

$this->title = 'Create User Product Type';
$this->params['breadcrumbs'][] = ['label' => 'User Product Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-product-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
